<?php
Wind::import('APPCENTER:service.srv.iPwInstall');
Wind::import('SRV:nav.dm.PwNavDm');
/**
 * 应用 - 导航安装
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwNavInstall.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package nav
 */
class PwNavInstall implements iPwInstall {
	private $link = '';

	/**
	 * 注册主导航
	 */
	public function install($install) {
		$r = $this->_install('main', $install);
		if ($r instanceof PwError) return $r;
		return true;
	}

	/**
	 * 注册底部导航
	 *
	 * @param unknown_type $install        	
	 */
	public function bottom($install) {
		$r = $this->_install('bottom', $install);
		if ($r instanceof PwError) return $r;
		return true;
	}

	/**
	 * 注册我的导航
	 *
	 * @param unknown_type $install        	
	 */
	public function my($install) {
		$r = $this->_install('my', $install);
		if ($r instanceof PwError) return $r;
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::backUp()
	 */
	public function backUp($install) {
		$ids = $install->getBackLog('nav');
		if ($ids) {
			$navs = $this->_navDs()->fetchNav($ids);
			$install->setRevertLog('nav', $navs);
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::revert()
	 */
	public function revert($install) {
		$navs = $install->getRevertLog('nav');
		if ($navs) {
			foreach ($navs as $k => $v) {
				$dm = new PwNavDm();
				$dm->setName($v['name'])->setLink($v['link'])->setSign($v['sign'])->setOrderid(
					$v['orderid'])->setIsshow($v['isshow'])->setTempid($v['tempid'])->setType(
					$v['type']);
				$this->_getNavDs()->addNav($dm);
			}
		}
		$this->update();
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::unInstall()
	 */
	public function unInstall($install) {
		$ids = $install->getInstallLog('nav');
		if ($ids) {
			foreach ($ids as $id) {
				$this->_navDs()->delNav($id);
			}
		}
		$this->update();
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::rollback()
	 */
	public function rollback($install) {
		$ids = $install->getInstallLog('nav');
		if ($ids) {
			foreach ($ids as $id) {
				$this->_navDs()->delNav($id);
			}
		}
		$this->update();
		return true;
	}

	/**
	 * Enter description here .
	 *
	 *
	 * ..
	 *
	 * @param unknown_type $type        	
	 * @param PwInstallApplication $install        	
	 * @return PwError boolean
	 */
	protected function _install($type, $install) {
		$manifest = $install->getManifest();
		if (!$this->link) {
			$this->link = 'index.php?m=app&app=' . $manifest->getApplication('alias');
		}
		$dm = new PwNavDm();
		$dm->setLink($this->link)->setType($type);
		if ($type == 'my') {
			$prefix = Wind::getComponent('i18n')->getMessage('ADMIN:nav.my.prefix');
			$dm->setName($prefix . $manifest->getApplication('name'))->setSign(
				$manifest->getApplication('alias'));
		} else {
			$dm->setName($manifest->getApplication('name'));
		}
		$id = $this->_navDs()->addNav($dm);
		if ($id instanceof PwError) return $id;
		$install->addInstallLog('nav', $id);
		$this->update();
		file_put_contents(DATA_PATH . 'tmp/log', 'nav!', FILE_APPEND);
		return true;
	}

	private function update() {
		return Wekit::load('nav.srv.PwNavService')->updateConfig();
	}

	/**
	 *
	 * @return PwNav
	 */
	private function _navDs() {
		return Wekit::load('nav.PwNav');
	}
}

?>