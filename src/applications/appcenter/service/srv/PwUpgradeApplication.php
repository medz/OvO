<?php
Wind::import('APPCENTER:service.srv.PwInstallApplication');
/**
 * 应用升级服务
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUpgradeApplication.php 25900 2013-03-26 10:35:44Z long.shi $
 * @package appcenter.service.srv
 */
class PwUpgradeApplication extends PwInstallApplication {

	protected $_backLog = array();
	protected $_revertLog = array();
	
	/**
	 * 纯在线应用升级
	 *
	 * @param string $id
	 * @return PwError|Ambigous <PwError, boolean>|boolean
	 */
	public function onlineUpgrade($id) {
		$this->_appId = $id;
		$manifest = $this->getOnlineInfo();
		$manifest['application']['alias'] = $id;
		if ($manifest instanceof PwError) return $manifest;
		if (true !== $_r = $this->initInstall($manifest)) return $_r;
		if (true !== $_r = $this->doUpgrade()) {
			$this->rollback();
			return $_r;
		}
		$this->clear();
		return true;
	}
	
	/**
	 * 在线升级服务统一调用入口
	 *
	 * 1. 下载升级包到本地
	 * 2. 校验hash值，检查合法性
	 * 4. 解压升级包
	 * 5. 解析应用配置
	 * 6. 校验版本是否合法，编码是否合法等
	 * 7. 包文件对比、覆盖
	 * 8. 升级 ：备份，恢复
	 *
	 * @param int $id        	
	 * @param string $hash        	
	 * @return PwError true
	 */
	public function upgrade($id) {
		$this->_appId = $id;
		$_r = $this->downloadInstallPack();
		if ($_r instanceof PwError) return $_r;
		$extends = $this->getOnlineInfo();
		if ($extends instanceof PwError) return $extends;
		
		if (true !== $_r = $this->extractPackage($_r)) return $_r;
		if (true !== $_r = $this->initInstall('', $extends)) return $_r;
		if (true !== $_r = $this->doUpgrade()) {
			$this->rollback();
			return $_r;
		}
		
		$this->clear();
		return true;
	}
	
	/**
	 * 升级流程
	 *
	 * @return PwError|boolean
	 */
	public function doUpgrade() {
		$this->backUp();
		try {
			list($service) = $this->resolvedInstallation();
			foreach ($service as $key => $var) {
				if (!isset($var['class'])) continue;
				$_install = Wekit::load($var['class']);
				if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
				$_m = empty($var['method']) ? 'install' : $var['method'];
				$r = $_install->$_m($this);
				if ($r instanceof PwError) return $r;
			}
			$this->log();
		} catch (Exception $e) {
			$error = $e->getMessage();
			is_array($error) || $error = array(
				'APPCENTER:install.fail', 
				array('{{error}}' => $e->getMessage()));
			return new PwError($error[0], $error[1]);
		}
		return true;
	}
	
	/**
	 * 获取安装流程注入
	 *
	 * @return array
	 */
	public function resolvedInstallation() {
		$service = $rollback = array();
		$conf = $this->getConfig('install-type', 
				$this->getManifest()->getApplication('type', 'app'));
		if (!empty($conf['step']['before'])) {
			foreach ($conf['step']['before'] as $var) {
				$var['class'] = $conf['class'];
				$service[] = $var;
			}
		} else
			$service[] = $conf;
			
		$rollback[] = $conf;
		foreach ($this->getManifest()->getInstallationService() as $var) {
			// TODO 从钩子中获取
			$_tmp = $this->getConfig('installation-service', $var);
			if (!$_tmp) continue;
			$rollback[] = $service[] = $_tmp;
			$this->addInstallLog('service', $_tmp);
		}
			
		if (!empty($conf['step']['after'])) {
			foreach ($conf['step']['after'] as $var) {
				$var['class'] = $conf['class'];
				$service[] = $var;
			}
		}
		
		$manifest = $this->getManifest()->getManifest();
		if (isset($manifest['install']) && $manifest['install']) {
			$_tmp = array('class' => $manifest['install']);
			//$service[] = $_tmp;
			$this->addInstallLog('service', $_tmp);
		}
		
		$this->addInstallLog('service', $conf);
		return array($service, $rollback);
	}
	
	/**
	 * 当应用安装发生错误时，回滚处理
	 *
	 * step 5
	 *
	 * @return oid
	 */
	public function rollback() {
		$rollback = $this->getInstallLog('service');
		foreach ($rollback as $var) {
			if (!isset($var['class'])) continue;
			$_install = Wekit::load($var['class']);
			if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
			$_install->rollback($this);
		}
		$this->revert();
	}
	
	/**
	 * 备份
	 *
	 */
	public function backUp() {
		$app_id = $this->_appId;
		if ($this->_appId[0] == '9') $app_id = substr($this->_appId, 1);
		$log = $this->_loadInstallLog()->findByAppId($app_id);
		foreach ($log as $value) {
			$this->_backLog[$value['log_type']] = $value['data'];
		}
		foreach ($this->_backLog['service'] as $key => $var) {
			if (!isset($var['class'])) continue;
			$_install = Wekit::load($var['class']);
			if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
			$r = $_install->backUp($this);
			if ($r instanceof PwError) return $r;
		}
		return true;
	}
	
	/**
	 * 恢复备份
	 *
	 */
	public function revert() {
		foreach ($this->_backLog['service'] as $key => $var) {
			if (!isset($var['class'])) continue;
			$_install = Wekit::load($var['class']);
			if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
			$r = $_install->revert($this);
			if ($r instanceof PwError) return $r;
		}
		return true;
	}
	
	/**
	 * 清理安装过程中产生的临时信息
	 *
	 * step 5
	 *
	 * @return void
	 */
	public function clear() {
		if (is_file($this->tmpInstallLog)) WindFile::del($this->tmpInstallLog);
		if ($this->tmpPackage) WindFolder::rm($this->tmpPackage, true);
		if ($this->tmpPath) WindFolder::rm($this->tmpPath, true);
	}
	
	/**
	 * 写升级日志
	 *
	 */
	public function log() {
		$this->_loadInstallLog()->delByAppId($this->_appId);
		$fields = array();
		foreach ($this->getInstallLog() as $key => $value) {
			$_tmp = array(
				'app_id' => $this->_appId,
				'log_type' => $key,
				'data' => $value,
				'created_time' => WEKIT_TIMESTAMP,
				'modified_time' => WEKIT_TIMESTAMP);
			$fields[] = $_tmp;
		}
		$this->_loadInstallLog()->batchAdd($fields);
	}
	
	/**
	 *
	 * @param string $key        	
	 */
	public function getBackLog($key) {
		return isset($this->_backLog[$key]) ? $this->_backLog[$key] : array();
	}
	
	/**
	 * @param string $key
	 * @param string|array $value
	 */
	public function setRevertLog($key, $value) {
		$this->_revertLog[$key] = $value;
	}
	
	/**
	 *
	 * @param string $key
	 */
	public function getRevertLog($key) {
		return isset($this->_revertLog[$key]) ? $this->_revertLog[$key] : array();
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