<?php
Wind::import("Lib:utility.PwCacheService");
/**
 * 域名service 
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDomainService.php 24341 2013-01-29 03:08:55Z jieyin $
 * @package domain.srv
 */
class PwDomainService {
	
	/**
	 * 获取二级域名设置的扩展
	 *
	 * @return array
	 */
	public function getDomainAddOns() {
		$r = @include Wind::getRealPath("APPS:rewrite.conf.domain.php", true);
		$r || $r = array();
		$r = PwSimpleHook::getInstance('domain_config')->runWithFilters($r);
		return $r;
	}
	
	/**
	 * 获取伪静态设置的扩展
	 *
	 * @return mixed
	 */
	public function getRewriteAddOns() {
		$r = @include Wind::getRealPath("APPS:rewrite.conf.rewrite.php", true);
		$r || $r = array();
		$r = PwSimpleHook::getInstance('rewrite_config')->runWithFilters($r);
		return $r;
	}
	
	/**
	 * 清除tpl缓存
	 *
	 * @return boolean
	 */
	public function refreshTplCache() {
		WindFolder::rm(Wind::getRealDir('DATA:compile'), true);
		WindFolder::rm(Wind::getRealDir('DATA:design.template'), true);
		return true;
	}
	
	/**
	 * 检测域名是否合法
	 *
	 * @param string $domain
	 * @param string $root 根域名
	 * @return PwError|boolean
	 */
	public function isDomainValid($domain, $root, $key) {
		$len = strlen($domain);
		if ($len < 3 || $len > 15) return new PwError('REWRITE:domain.length');
		if ($domain[0] < 'a' || $domain[0] > 'z') return new PwError('REWRITE:domain.first.illegal');
		if (!preg_match('/^[a-z0-9]+$/', $domain)) return new PwError('REWRITE:domain.char.num');
		$result = $this->_domainDs()->getByDomainAndRoot($domain, $root);
		if ($result && $result['domain_key'] != $key)
			return new PwError('REWRITE:domain.exist');
		$domain_hold = Wekit::C('domain', 'domain.hold');
		$holds = explode(',', $domain_hold);
		foreach ($holds as $v) {
			$preg = str_replace('*', '.*?', $v);
			if (preg_match("/^$preg$/i", $domain))
				return new PwError(array('REWRITE:domain.hold', array($v)));
		}
		return true;
	}
	
	/**
	 * 检测域名是否合法
	 *
	 * @param unknown_type $domain
	 * @param unknown_type $type
	 * @param unknown_type $key
	 * @return PwError|boolean
	 */
	public function isNameValid($domain, $key) {
		$len = strlen($domain);
		if ($len < 3 || $len > 15) return new PwError('REWRITE:domain.length');
		if ($domain[0] < 'a' || $domain[0] > 'z') return new PwError('REWRITE:domain.first.illegal');
		if (!preg_match('/^[a-z0-9]+$/', $domain)) return new PwError('REWRITE:domain.char.num');
		$result = $this->_domainDs()->getByDomain($domain);
		if (count($result) > 1 || ($result && $result[0]['domain_key'] != $key))
			return new PwError('REWRITE:domain.exist');
		$domain_hold = Wekit::C('domain', 'domain.hold');
		$holds = explode(',', $domain_hold);
		foreach ($holds as $v) {
			$preg = str_replace('*', '.*?', $v);
			if (preg_match("/^$preg$/i", $domain))
				return new PwError(array('REWRITE:domain.hold', array($v)));
		}
		return true;
	} 
	
	/**
	 * 更新域名所有缓存
	 *
	 */
	public function flushAll() {
		$this->flushDomain();
		$this->refreshTplCache();
	}
	
	/**
	 * 更新某个首字母的缓存
	 *
	 * @param char $first
	 */
	public function flushDomain() {
		$domain = $app = array();
		$forum_isopen = Wekit::C('domain', "forum.isopen");
		$result = $this->_domainDs()->getAll();
		foreach ($result as $v) {
			if ($v['domain_type'] == 'forum' && !$forum_isopen) continue;
			$k = 'http://'. $v['domain'] . '.' . $v['root'];
			$domain[$k] = $v['domain_key'];
			if ($v['domain_type'] == 'app') {
				list(, , $m) = WindUrlHelper::resolveAction($v['domain_key']);
				$app[$m] = $k;
			}
		}
		Wekit::C()->setConfig('site', 'domain', $domain);
		Wekit::C()->setConfig('site', 'domain.app', $app);
	}
	
	/**
	 * @return PwDomain
	 */
	private function _domainDs() {
		return Wekit::load('domain.PwDomain');
	}
}

?>