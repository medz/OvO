<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 二级域名
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: DomainController.php 24202 2013-01-23 02:18:05Z jieyin $
 * @package rewrite.admin
 */
class DomainController extends AdminBaseController {

	/*
	 * (non-PHPdoc) @see WindController::run()
	 */
	public function run() {
		$root = Wekit::C('site', 'cookie.domain');
		if ($root) {
			$domain = Wekit::C('domain');
			$addons = $this->_service()->getDomainAddOns();
			foreach ($addons as $k => $v) {
				$r = $this->_ds()->getByDomainKey($v[1] ? $v[1] : "$k/index/run");
				$addons[$k][] = isset($r['domain']) ? $r['domain'] : '';
			}
			$this->setOutput($domain, 'domain');
			$this->setOutput($addons, 'addons');
			$root[0] != '.' && $root = '.' . $root;
		}
		$this->setOutput($root, 'root');
	}

	/**
	 * 保存修改
	 */
	public function doModifyAction() {
		$root = Wekit::C('site', 'cookie.domain');
		if (empty($root)) $this->showError('REWRITE:cookie.domain.fail');
		$root[0] != '.' && $root = '.' . $root;
		list($app, $domain, $domain_hold) = $this->getInput(
			array('app', 'domain', 'domain_hold'));
		
		// 判断域名是否重复
		$unique = array();
		Wind::import('SRV:domain.dm.PwDomainDm');
		$bo = new PwConfigSet('domain');
		$addons = $this->_service()->getDomainAddOns();
		foreach ($app as $key => $value) {
			$domainKey = $addons[$key][1] ? $addons[$key][1] : "$key/index/run";
			if ($value) {
				//域名重复
				in_array($value, $unique) && $this->showError(
				array('REWRITE:domain.same', array($value)));
				$unique[] = $value;
				//添加应用域名
				$dm = new PwDomainDm();
				$dm->setDomain($value)->setRoot(substr($root, 1))->setDomainKey(
					$domainKey)->setDomainType('app')->setFirst($value[0]);
				$r = $this->_ds()->replaceDomain($dm);
				if ($r instanceof PwError) $this->showError($r->getError());
			} else {
				$this->_ds()->deleteByDomainKey($domainKey);
			}
		}
		
		$unique = array();
		$space_root = '';
		$siteBo = new PwConfigSet('site');
		foreach ($domain as $k => $v) {
			$domain_root = '';
			if ($v['isopen']) {
				if (!$app['default']) $this->showError('REWRITE:default.empty');
				if ($k == 'space' && !$v['root']) $this->showError('REWRITE:root.empty');
				$space_root = isset($domain['space']['root']) ? $domain['space']['root'] : '';
				if ($k != 'space' && $domain['space']['isopen']) {
					if ($v['root'] == $space_root) {
						$this->showError('REWRITE:root.same');
					}
				}
				$unique[] = $v['root'];
				$domain_root = $v['root'] ? $v['root'] . $root : substr($root, 1);
				$dm = new PwDomainDm();
				$dm->setRoot($domain_root)->setDomainType($k);
				$r = $this->_ds()->updateByDomainType($dm);
				if ($r instanceof PwError) $this->showError($r->getError());
				
			} elseif ($k != 'forum') {
				$this->_ds()->deleteByDomainType($k);
			}
			$siteBo->set("domain.$k.isopen", $v['isopen']);
			$bo->set("$k.isopen", $v['isopen'])->set("$k.root", $domain_root);
		}
		$bo->set('domain.hold', $domain_hold);
		$bo->flush();
		$siteBo->set('domain.space.root', $space_root);
		$siteBo->flush();
		$this->_service()->flushAll();
		Wekit::load('SRV:nav.srv.PwNavService')->updateConfig();
		$this->showMessage('success');
	}

	/**
	 * @return PwDomain
	 */
	private function _ds() {
		return Wekit::load('domain.PwDomain');
	}
	
	/**
	 * @return PwDomainService
	 */
	private function _service() {
		return Wekit::load('domain.srv.PwDomainService');
	}
}

?>