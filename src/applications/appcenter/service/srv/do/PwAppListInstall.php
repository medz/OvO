<?php
Wind::import('APPCENTER:service.srv.iPwInstall');
/**
 * 前台应用中心列表注册
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwAppListInstall.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package appcenter.service.srv.do
 */
class PwAppListInstall implements iPwInstall {
	/*
	 * (non-PHPdoc) @see iPwInstall::install()
	 */
	public function install($install) {
		$list = Wekit::C()->site->get('appList', array());
		$appId = $install->getAppId();
		$manifest = $install->getManifest()->getManifest();
		$url = isset($manifest['front-url']) ? $manifest['front-url'] : $manifest['application']['alias'] . '/index/run';
		$list = array($appId => $url) + $list;
		Wekit::C()->setConfig('site', 'appList', $list);
		$install->setInstallLog('appList', array($appId => $url));
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::backUp()
	 */
	public function backUp($upgrade) {
		if ($list = $upgrade->getBackLog('appList')) {
			$upgrade->setRevertLog('appList', $list);
			$appList = Wekit::C()->site->get('appList', array());
			unset($appList[key($list)]);
			Wekit::C()->setConfig('site', 'appList', $appList);
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::revert()
	 */
	public function revert($upgrade) {
		if ($list = $upgrade->getRevertLog('appList')) {
			$appList = Wekit::C()->site->get('appList', array());
			$appList = $list + $appList;
			Wekit::C()->setConfig('site', 'appList', $appList);
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::unInstall()
	 */
	public function unInstall($uninstall) {
		if ($list = $uninstall->getInstallLog('appList')) {
			$appList = Wekit::C()->site->get('appList', array());
			unset($appList[key($list)]);
			Wekit::C()->setConfig('site', 'appList', $appList);
		}
		return true;
	}
	
	/*
	 * (non-PHPdoc) @see iPwInstall::rollback()
	 */
	public function rollback($install) {
		if ($list = $install->getInstallLog('appList')) {
			$appList = Wekit::C()->site->get('appList', array());
			unset($appList[key($list)]);
			Wekit::C()->setConfig('site', 'appList', $appList);
		}
		return true;
	}
}
?>