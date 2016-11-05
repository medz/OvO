<?php
Wind::import('APPCENTER:service.dm.PwStyleDm');
/**
 * 风格安装初始化
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyleInit.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package src.applications.appcenter
 */
class PwStyleInit {
	
	/**
	 * 安装过程中风格的初始化
	 *
	 */
	public function init() {
		$install = Wekit::load('APPCENTER:service.srv.PwInstallApplication');
		$configBo = new PwConfigSet('site');
		$config = $install->getConfig('style-type');
		foreach ($config as $k => $v) {
			$configBo->set("theme.$k.pack", $v[1]);
			$pack = Wind::getRealDir('THEMES:' . str_replace('/', '.', $v[1]) . '.default');
			$id = $this->install($pack);
			if (!$id) continue;
			$dm = new PwStyleDm();
			$dm->setAppid($id)->setIsCurrent(1);
			$this->_styleDs()->updateStyle($dm);
			$configBo->set("theme.$k.default", 'default');
		}
		$configBo->flush();
	}
	
	protected function install($pack) {
		$manifest = $pack . '/Manifest.xml';
		if (!is_file($manifest)) return false;
		/* @var $install PwInstallApplication */
		Wind::import('APPCENTER:service.srv.PwInstallApplication');
		$install = new PwInstallApplication();
		/* @var $_install PwStyleInstall */
		$_install = Wekit::load('APPCENTER:service.srv.do.PwStyleInstall');
		$conf = $install->getConfig('install-type', 'style');
		$r = $install->initInstall($manifest);
		if ($r instanceof PwError) return false;
		$r = $_install->install($install);
		if ($r instanceof PwError) return false;
		$r = $_install->registeApplication($install);
		if ($r instanceof PwError) return false;
		$install->addInstallLog('packs', $pack);
		$install->addInstallLog('service', $conf);
		$fields = array();
		foreach ($install->getInstallLog() as $key => $value) {
			$_tmp = array(
				'app_id' => $install->getAppId(),
				'log_type' => $key,
				'data' => $value,
				'created_time' => time(),
				'modified_time' => time());
			$fields[] = $_tmp;
		}
		Wekit::load('APPCENTER:service.PwApplicationLog')->batchAdd($fields);
		return $install->getAppId();
	}
	
	/**
	 *
	 * @return PwStyle
	 */
	private function _styleDs() {
		return Wekit::load('APPCENTER:service.PwStyle');
	}
}

?>
