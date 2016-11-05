<?php
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
Wind::import('APPCENTER:service.srv.helper.PwManifest');
/**
 * 卸载应用
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUninstallApplication.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package products
 * @subpackage appcenter.service.srv
 */
class PwUninstallApplication {
	protected $_appId = '';
	private $_log = array();

	/**
	 * @param string $appId
	 */
	public function uninstall($appId) {
		$this->_appId = $appId;
		$log = $this->_loadInstallLog()->findByAppId($this->_appId);
		foreach ($log as $value) {
			$this->_log[$value['log_type']] = $value['data'];
		}
		$service = $this->getInstallLog('service');
		if (!$service) return $this->forceUninstall($appId);
		foreach ($service as $key => $var) {
			if (!isset($var['class'])) continue;
			try {
				$_install = Wekit::load($var['class']);
			} catch (PwException $e) {
				continue;
			}
			if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
			$r = $_install->unInstall($this);
			if ($r instanceof PwError) return $r;
		}
		$this->_loadInstallLog()->delByAppId($this->_appId);
		return true;
	}
	
	/**
	 * 强制清理
	 *
	 * @param unknown_type $appId
	 * @return boolean
	 */
	public function forceUninstall($appId) {
		$app = $this->_loadDs()->findByAppId($appId);
		if (empty($app)) {
			return true;
		}
		$this->_loadPwHookInject()->deleteByAppId($app['alias']);
		$this->_loadDs()->delByAppId($appId);
		return true;
	}
	
	/**
	 * $key 值:
	 * service 安装服务
	 * appId 应用ID
	 * hook	 已安装的hook
	 * inject 已注册的inject
	 * table 已安装的数据表
	 * 
	 * @param string $key
	 */
	public function getInstallLog($key) {
		return isset($this->_log[$key]) ? $this->_log[$key] : array();
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->_hash;
	}

	/**
	 * @return string
	 */
	public function getAppId() {
		return $this->_appId;
	}

	/**
	 * @return PwApplicationLog
	 */
	private function _loadInstallLog() {
		return Wekit::load('APPCENTER:service.PwApplicationLog');
	}
	
	/**
	 * @return PwApplication
	 */
	private function _loadDs() {
		return Wekit::load('APPCENTER:service.PwApplication');
	}
	
	/**
	 *
	 * @return PwHookInject
	 */
	private function _loadPwHookInject() {
		return Wekit::load('SRV:hook.PwHookInject');
	}
}

?>