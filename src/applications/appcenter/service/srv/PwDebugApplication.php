<?php
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
Wind::import('APPCENTER:service.srv.helper.PwManifest');
/**
 * 开发者调试应用
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDebugApplication.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package wind
 */
class PwDebugApplication {
	private $app_id;
	/**
	 *
	 * @var PwManifest
	 */
	private $manifest;
	private $app;

	public function inDevMode1() {
		$dev = Wekit::C('site', 'debug');
		return in_array($dev, array('520', '1314'));
	}

	public function inDevMode2() {
		$dev = Wekit::C('site', 'debug');
		return '1314' == $dev;
	}

	/**
	 * do ---> go
	 *
	 * @return void boolean
	 */
	public function compile($force = false) {
		if (!$this->inDevMode1()) return;
		$manifests = $this->_read();
		if (empty($manifests)) return;
		$logs = Wekit::cache()->get('app_debug');
		$logs || $logs = array();
		foreach ($manifests as $alias => $manifest) {
			$this->_copyRes($alias, $manifest);
			if (!isset($logs[$alias]) || md5_file($manifest) != $logs[$alias] || $force) {
				$this->_upgrade($alias, $manifest);
				$logs[$alias] = md5_file($manifest);
			}
		}
		Wekit::cache()->set('app_debug', $logs);
		return true;
	}

	/**
	 * 调试应用
	 *
	 * @param unknown_type $alias        	
	 * @param unknown_type $manifest        	
	 */
	private function _upgrade($alias, $manifest) {
		// 更新基本信息
		Wind::import('APPCENTER:service.dm.PwApplicationDm');
		$man_array = $this->manifest->getManifest();
		$dm = new PwApplicationDm();
		$dm->setAppId($this->app_id);
		$dm->setName($man_array['application']['name']);
		$dm->setDescription($man_array['application']['description']);
		$dm->setVersion($man_array['application']['version']);
		$dm->setPwVersion($man_array['application']['pw-version']);
		$dm->setAuthorName($man_array['application']['author-name']);
		$dm->setAuthorEmail($man_array['application']['author-email']);
		$dm->setWebsite($man_array['application']['website']);
		$dm->setLogo($man_array['application']['logo']);
		Wekit::load('APPCENTER:service.PwApplication')->update($dm);
		
		$this->_loadPwHooks()->delByAppId($this->app_id);
		$this->_loadPwHookInject()->deleteByAppId($alias);
		$hooks = $this->manifest->getHooks();
		$log = array();
		if ($hooks) {
			foreach ($hooks as $key => $hook) {
				$hook['app_id'] = $this->app_id;
				$hook['app_name'] = $this->app['app_name'];
				$hooks[$key] = $hook;
			}
			$this->_loadPwHooks()->batchAdd($hooks);
			$log[] = array(
				'app_id' => $this->app_id, 
				'log_type' => 'hook', 
				'data' => array_keys($hooks), 
				'created_time' => WEKIT_TIMESTAMP, 
				'modified_time' => WEKIT_TIMESTAMP);
		}
		$inject = $this->manifest->getInjectServices();
		if ($inject) {
			$inject_ids = array();
			foreach ($inject as $key => &$value) {
				$value['app_id'] = $alias;
				$value['app_name'] = $this->app['app_name'];
			}
			$this->_loadPwHookInject()->batchAdd($inject);
			$injects = $this->_loadPwHookInject()->findByAppId($alias);
			$log[] = array(
				'app_id' => $this->app_id, 
				'log_type' => 'inject', 
				'data' => array_keys($injects), 
				'created_time' => WEKIT_TIMESTAMP, 
				'modified_time' => WEKIT_TIMESTAMP);
		}
		
		$log && $this->_loadInstallLog()->batchAdd($log);
	}

	/**
	 * 复制资源文件
	 *
	 * @param unknown_type $alias        	
	 * @param unknown_type $manifest        	
	 * @return boolean
	 */
	private function _copyRes($alias, $manifest) {
		$this->app = $this->_appDs()->findByAlias($alias);
		$this->app_id = $this->app['app_id'];
		$this->manifest = new PwManifest($manifest);
		$man_array = $this->manifest->getManifest();
		$log = array();
		if ($man_array['res']) {
			$source = dirname($manifest) . DIRECTORY_SEPARATOR . str_replace('.', 
				DIRECTORY_SEPARATOR, $man_array['res']);
			$targetPath = Wind::getRealDir('THEMES:extres.' . $alias, true);
			if (!is_dir($source)) return false;
			PwApplicationHelper::copyRecursive($source, $targetPath);
			$app_log = $this->_loadInstallLog()->findByAppId($this->app_id);
			$packs_log = array();
			foreach ($app_log as $v) {
				if ($v['log_type'] == 'packs') $packs_log = $v['data'];
			}
			if (!in_array($targetPath, $packs_log)) $packs_log[] = $targetPath;
			$log[] = array(
				'app_id' => $this->app_id, 
				'log_type' => 'packs', 
				'data' => $packs_log, 
				'created_time' => WEKIT_TIMESTAMP, 
				'modified_time' => WEKIT_TIMESTAMP);
		}
		$log && $this->_loadInstallLog()->batchAdd($log);
	}

	/**
	 * 读取目录
	 *
	 * @return multitype:string
	 */
	private function _read() {
		$ext = Wind::getRealDir('EXT:', true);
		$dirs = WindFolder::read($ext, WindFolder::READ_DIR);
		$manifests = array();
		$result = array_keys($this->_appDs()->fetchByAlias($dirs, 'alias'));
		/*
		 * $to_install = array_diff($dirs, $result); foreach ($to_install as
		 * $pack) { if ($pack[0] == '.') continue; $this->_toinstall($ext .
		 * DIRECTORY_SEPARATOR . $pack); }
		 */
		foreach ($result as $k) {
			$manifest = $ext . DIRECTORY_SEPARATOR . $k . DIRECTORY_SEPARATOR . 'Manifest.xml';
			if (!is_file($manifest)) continue;
			$manifests[$k] = $manifest;
		}
		return $manifests;
	}

	/**
	 * 扫描完安装
	 *
	 * @param unknown_type $pack        	
	 */
	public function installPack($pack) {
		Wind::import('APPCENTER:service.srv.PwInstallApplication');
		$install = new PwInstallApplication();
		/* @var $_install PwInstall */
		$_install = Wekit::load('APPCENTER:service.srv.do.PwInstall');
		$conf = $install->getConfig('install-type', 'app');
		$manifest = $pack . '/Manifest.xml';
		if (!is_file($manifest)) return $this->_e(null, 
			new PwError('file.not.exist - ' . $manifest));
		$install->setTmpPackage($pack);
		$r = $install->initInstall($manifest);
		if ($r instanceof PwError) return $this->_e(null, $r);
		
		try {
			$r = $_install->install($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
			$r = $_install->registeHooks($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
			$r = $_install->registeInjectServices($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
			$r = $_install->registeData($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
			foreach ($install->getManifest()->getInstallationService() as $var) {
				$_tmp = $install->getConfig('installation-service', $var);
				if (!$_tmp) continue;
				$toinstall = Wekit::load($_tmp['class']);
				if (!$toinstall instanceof iPwInstall) continue;
				$_tmp['_key'] = $var;
				$_m = empty($_tmp['method']) ? 'install' : $_tmp['method'];
				$r = $toinstall->$_m($install);
				if ($r instanceof PwError) return $this->_e($install, $r);
				$install->addInstallLog('service', $_tmp);
			}
			$manifest = $install->getManifest()->getManifest();
			if (isset($manifest['install']) && $manifest['install']) {
				$_tmp = array('class' => $manifest['install']);
				$toinstall = Wekit::load($manifest['install']);
				if (!$toinstall instanceof iPwInstall) continue;
				$r = $toinstall->install($install);
				if ($r instanceof PwError) return $this->_e($install, $r);
				$install->addInstallLog('service', $_tmp);
			}
			$r = $_install->registeResource($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
			$r = $_install->registeApplication($install);
			if ($r instanceof PwError) return $this->_e($install, $r);
		} catch (Exception $e) {
			$error = $e->getMessage();
			is_array($error) || $error = array(
				'APPCENTER:install.fail', 
				array('{{error}}' => $e->getMessage()));
			return $this->_e($install, new PwError($error[0], $error[1]));
		}
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
	}

	private function _e($install, $r) {
		$install && $install->rollback();
		return $r;
		$lang = Wind::getComponent('i18n');
		$error = $r->getError();
		$var = array();
		if (is_array($error)) {
			list($error, $var) = $error;
		}
		Wind::getApp()->getResponse()->setBody(
			'<div class="tips">' . $lang->getMessage($error, $var) . '</div>', 'install');
	}

	/**
	 *
	 * @return PwHookInject
	 */
	private function _loadPwHookInject() {
		return Wekit::load('SRV:hook.PwHookInject');
	}

	/**
	 *
	 * @return PwHooks
	 */
	private function _loadPwHooks() {
		return Wekit::load('SRV:hook.PwHooks');
	}

	/**
	 *
	 * @return PwApplication
	 */
	private function _appDs() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}

	/**
	 *
	 * @return PwApplicationLog
	 */
	private function _loadInstallLog() {
		return Wekit::load('APPCENTER:service.PwApplicationLog');
	}
}

?>