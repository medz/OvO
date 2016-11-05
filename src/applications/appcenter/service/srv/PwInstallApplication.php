<?php
Wind::import('APPCENTER:service.srv.helper.PwApplicationHelper');
Wind::import('APPCENTER:service.srv.helper.PwManifest');
/**
 * pw 系统应用安装服务
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwInstallApplication.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package products
 * @subpackage appcenter.service.srv
 */
class PwInstallApplication {
	const CONF_PATH = 'APPCENTER:conf.install.php';
	/**
	 * 安装包临时存放位置
	 *
	 * @var string
	 */
	protected $tmpPath = '';
	protected $tmpPackage = '';
	protected $tmpInstallLog = '';
	/**
	 * 应用配置信息
	 *
	 * @var array
	 */
	protected $_config = array();
	/**
	 *
	 * @var PwManifest
	 */
	protected $_manifest = null;
	protected $_appId = '';
	private $_hash = '';
	private $_log = array();
	private $_step = false;

	/**
	 * 初始化安装程序
	 */
	public function __construct() {
		$this->_appId = 'L000' . time() . WindUtility::generateRandStr(4);
		$this->_config = @include (Wind::getRealPath(self::CONF_PATH, true));
		$this->tmpPath = Wind::getRealPath($this->getConfig('tmp_dir') . '.' . Pw::getTime(), false);
		$this->tmpInstallLog = Wind::getRealPath($this->getConfig('log_dir'), false);
	}

	/**
	 * 在线安装服务统一调用入口
	 *
	 * 1. 校验ID，是否合法，是否已经安装
	 * 2. 下载安装包到本地
	 * 3. 校验hash值，检查合法性
	 * 4. 解压安装包
	 * 5. 解析应用配置
	 * 6. 校验版本是否合法，编码是否合法等
	 *
	 * @param int $id        	
	 * @param string $hash        	
	 * @return PwError true
	 */
	public function install($id) {
		$this->_appId = $id;
		$_r = $this->download();
		if ($_r instanceof PwError) return $_r;
		file_put_contents(DATA_PATH . 'tmp/log', 'Download!');
		$extends = $this->getOnlineInfo();
		if ($extends instanceof PwError) return $extends;
		file_put_contents(DATA_PATH . 'tmp/log', 'getOnlineInfo!', FILE_APPEND);
		if (true !== $_r = $this->initInstall('', $extends)) return $_r;
		file_put_contents(DATA_PATH . 'tmp/log', 'initInstall!', FILE_APPEND);
		if (true !== $_r = $this->doInstall('all', $this->_hash)) {
			$this->rollback();
			return $_r;
		}
		
		$this->clear();
		return true;
	}

	/**
	 * 纯在线应用安装服务接口统一调用入口
	 *
	 * @param string $id
	 *        	appID
	 * @return PwError true
	 */
	public function onlineInstall($id) {
		$this->_appId = $id;
		$manifest = $this->getOnlineInfo();
		if ($manifest instanceof PwError) return $manifest;
		$manifest['application']['alias'] = $id;
		if (true !== $_r = $this->initInstall(array(), $manifest)) return $_r;
		if (true !== $_r = $this->doInstall('all', $this->_hash)) {
			$this->rollback();
			return $_r;
		}
		$this->clear();
		return true;
	}

	/**
	 * 本地化安装统一调用入口
	 *
	 * @param string $installPack        	
	 * @return PwError true
	 */
	public function localInstall($installPack) {
		if (true !== ($_r = $this->extractPackage($installPack))) return $_r;
		if (true !== $_r = $this->initInstall()) return $_r;
		if (true !== $_r = $this->doInstall('all', $this->_hash)) {
			$this->rollback();
			return $_r;
		}
		
		$this->clear();
		return true;
	}

	/**
	 * 解压压缩包
	 *
	 * step 2
	 * 
	 * @param string $packageFile        	
	 * @return true PwError
	 */
	public function extractPackage($packageFile) {
		$this->_hash = md5_file($packageFile);
		$this->tmpPackage = PwApplicationHelper::extract($packageFile, $this->tmpPath);
		if ($this->tmpPackage === false || !is_dir($this->tmpPackage)) return new PwError(
			'APPCENTER:install.checkpackage.format.fail', array('{{error}}' => $this->tmpPackage));
		return true;
	}

	/**
	 * 初始化安装信息，并注册安装程序到安装流程中
	 * 注册应用安装信息，到应用安装流程中
	 *
	 * step 3
	 * 
	 * @param array $manifest        	
	 * @return PwError true
	 */
	public function initInstall($manifest = '', $extends = array()) {
		if ($manifest === '') {
			$manifest = $this->tmpPackage . '/' . $this->getConfig('manifest');
			if (!is_file($manifest)) return new PwError('APPCENTER:install.mainfest.not.exist');
		} 
		
		$this->_manifest = new PwManifest($manifest, $extends);
		// TODO 校验系统编码是否合法 校验版本是否合法 校验信息是否合法 依赖校验
		// 'APPCENTER:install.manifest.fail'
		return true;
	}

	/**
	 * 执行安装过程，返回是否安装成功
	 *
	 * step 4
	 * 接收安装包目录
	 * 1. 检查并解压安装包到临时目录下，检查安装包的格式以及完整性
	 * 3. 初始化挂载安装处理程序
	 * 2. 将安装包移动到目标位置
	 * 3. 注册相应的安装程序进行安装
	 * 
	 * @param string $step
	 *        	需要执行的安装步骤
	 * @return PwError true next
	 */
	public function doInstall($step, $hash) {
		try {
			$this->tmpInstallLog .= '/' . $hash . '.log';
			$this->_step = ($step !== 'all');
			/* @var $install PwInstallApplication */
			list($service, , $install) = $this->resolvedInstallation($this->tmpInstallLog);
			if (empty($service)) return new PwError('APPCENTER:install.service.fail');
			$this->_appId = $install->getAppId();
			
			$next = true;
			if ($step !== 'all') {
				if (!isset($service[$step])) return new PwError('APPCENTER:install.step.fail');
				isset($service[$step + 1]) && $next = array(
					$step + 1, 
					$service[$step + 1]['message']);
				$service = array($service[$step]);
			}
			foreach ($service as $key => $var) {
				if (!isset($var['class'])) continue;
				$_install = Wekit::load($var['class']);
				if (!$_install instanceof iPwInstall) return new PwError(
					'APPCENTER:install.classtype');
				$_m = empty($var['method']) ? 'install' : $var['method'];
				$r = $_install->$_m($install);
				if ($r instanceof PwError) return $r;
			}
			if ($next !== true) {
				$installation = array('installation' => base64_encode(serialize($install)));
				PwApplicationHelper::writeInstallLog($this->tmpInstallLog, $installation, true);
			} else {
				$fields = array();
				foreach ($install->getInstallLog() as $key => $value) {
					$_tmp = array(
						'app_id' => $install->getAppId(), 
						'log_type' => $key, 
						'data' => $value, 
						'created_time' => WEKIT_TIMESTAMP, 
						'modified_time' => WEKIT_TIMESTAMP);
					$fields[] = $_tmp;
				}
				$this->_loadInstallLog()->batchAdd($fields);
			}
			return $next;
		} catch (Exception $e) {
			$error = $e->getMessage();
			is_array($error) || $error = array(
				'APPCENTER:install.fail', 
				array('{{error}}' => $e->getMessage()));
			return new PwError($error[0], $error[1]);
		}
	}

	/**
	 * 当应用安装发生错误时，回滚处理
	 *
	 * step 5
	 * 
	 * @return oid
	 */
	public function rollback() {
		list(, $rollback, $install) = $this->resolvedInstallation($this->tmpInstallLog);
		foreach ($rollback as $var) {
			if (!isset($var['class'])) continue;
			$_install = Wekit::load($var['class']);
			if (!$_install instanceof iPwInstall) return new PwError('APPCENTER:install.classtype');
			$_install->rollback($install);
		}
	}

	/**
	 * 清理安装过程中产生的临时信息
	 *
	 * step 5
	 * 
	 * @return void
	 */
	public function clear() {
		list(, , $install) = $this->resolvedInstallation($this->tmpInstallLog);
		if (is_file($this->tmpInstallLog)) WindFile::del($this->tmpInstallLog);
		if ($install->getTmpPackage()) WindFolder::rm($install->getTmpPackage(), true);
		if ($install->getTmpPath()) WindFolder::rm($install->getTmpPath(), true);
	}

	/**
	 *
	 * @param string $key        	
	 */
	public function getInstallLog($key = '') {
		return $key === '' ? $this->_log : (isset($this->_log[$key]) ? $this->_log[$key] : array());
	}

	/**
	 * 收集安装日志
	 *
	 * @param string $key        	
	 * @param array $value        	
	 */
	public function setInstallLog($key, $value) {
		$this->_log[$key] = $value;
	}

	/**
	 * 收集安装日志
	 *
	 * @param string $key        	
	 * @param array $value        	
	 */
	public function addInstallLog($key, $value) {
		if (!isset($this->_log[$key])) $this->_log[$key] = array();
		$this->_log[$key][] = $value;
	}
	
	/**
	 * 下载应用
	 *
	 * @return boolean|PwError
	 */
	public function download() {
		if (function_exists('gzinflate')) {
			$r = $this->downloadInstallPack();
			if ($r instanceof PwError) {
				return $r;
			}
			$r = $this->extractPackage($r);
			if ($r instanceof PwError) {
				return $r;
			}
		} else {
			$r = $this->downloadFiles();
			if ($r instanceof PwError) return $r;
		}
		return true;
	}
	
	/**
	 * 单文件下载
	 *
	 * @return PwError
	 */
	public function downloadFiles() {
		return new PwError('APPCENTER:unsupport.zip');
	}

	/**
	 * 下载应用安装包，并进行hash校验，返回下载后包地址
	 *
	 * step 1
	 * 
	 * @return string PwError
	 */
	public function downloadInstallPack() {
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'getDownLoadUrl', 'appid' => $this->_appId));
		$info = PwApplicationHelper::requestAcloudData($url);
		if ($info['code'] !== '0') return new PwError('APPCENTER:install.download.fail', 
			array('{{error}}' => $info['msg']));
		list($bool, $package) = PwApplicationHelper::requestAcloudData($info['info']['download'], 
			$this->tmpPath);
		if (!$bool) return new PwError('APPCENTER:install.download.fail', array('{{error}}' => $package));
		if ($info['info']['hash'] !== md5_file($package)) {
			return new PwError('APPCENTER:install.checkpackage.fail');
		}
		$this->_hash = $info['hash'];
		return $package;
	}
	
	/**
	 * 获取线上应用基本信息，包括应用中心提交应用填写的描述、logo等
	 *
	 * @return PwError|array  
	 */
	public function getOnlineInfo() {
		$url = PwApplicationHelper::acloudUrl(
			array('a' => 'forward', 'do' => 'getAppById', 'appid' => $this->_appId));
		$data = PwApplicationHelper::requestAcloudData($url);
		if ($data['code'] !== '0') return new PwError('APPCENTER:install.fail',
			array('{{error}}' => $data['msg']));
		$manifest = array(
			'application' => array(
				'name' => $data['info']['app_name'],
				'version' => $data['info']['version'],
				'pw-version' => $data['info']['bbs_version'],
				'description' => trim($data['info']['description'], '\'"'),
				'logo' => $data['info']['icon'],
				'author-name' => trim($data['info']['app_author'], '\'"'),
				'website' => $data['info']['author_url'],
				'charset' => ACloudSysCoreCommon::getGlobal('g_charset')
				));
		return $manifest;
	}

	/**
	 * 获取配置值
	 *
	 * @return mixed
	 */
	public function getConfig($configName = '', $subConfigName = '', $default = '') {
		if ($configName === '') return $this->_config;
		if (!isset($this->_config[$configName])) return $default;
		if ($subConfigName === '') return $this->_config[$configName];
		if (!isset($this->_config[$configName][$subConfigName])) return $default;
		return $this->_config[$configName][$subConfigName];
	}

	/**
	 * 获取安装流程注入
	 *
	 * @return array
	 */
	protected function resolvedInstallation($file) {
		$install = null;
		if ($this->_step && is_file($file)) {
			$service = PwApplicationHelper::readInstallLog($file, 'services');
			$rollback = PwApplicationHelper::readInstallLog($file, 'rollback');
			$install = PwApplicationHelper::readInstallLog($file, 'installation');
			$install = unserialize(base64_decode($install));
		} else {
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
				$_tmp['_key'] = $var;
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
				$service[] = $_tmp;
				$this->addInstallLog('service', $_tmp);
			}
			
			$this->addInstallLog('service', $conf);
			if ($this->_step) {
				PwApplicationHelper::writeInstallLog($file, 
					array(
						'services' => $service, 
						'rollback' => $rollback, 
						'installation' => base64_encode(serialize($this))));
			}
			$install = $this;
		}
		return array($service, $rollback, $install);
	}

	/**
	 *
	 * @return PwManifest
	 */
	public function getManifest() {
		return $this->_manifest;
	}

	/**
	 *
	 * @return string
	 */
	public function getTmpPath() {
		return $this->tmpPath;
	}

	/**
	 *
	 * @param string $path        	
	 */
	public function setTmpPath($path) {
		$this->tmpPath = $path;
	}

	/**
	 *
	 * @return string
	 */
	public function getTmpPackage() {
		return $this->tmpPackage;
	}
	
	/**
	 *
	 * @param string
	 */
	public function setTmpPackage($package) {
		$this->tmpPackage = $package;
	}

	/**
	 *
	 * @return string
	 */
	public function getHash() {
		return $this->_hash;
	}

	/**
	 *
	 * @return string
	 */
	public function getAppId() {
		return $this->_appId;
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