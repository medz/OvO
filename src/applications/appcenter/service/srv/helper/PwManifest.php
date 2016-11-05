<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package wind
 */
class PwManifest {
	private $manifest;
	/**
	 * 应用配置信息
	 *
	 * @var array
	 */
	private $application = null;
	/**
	 * 注册安装服务
	 *
	 * @var array
	 */
	private $installationService = null;
	/**
	 * 钩子信息
	 *
	 * @var array
	 */
	private $hooks = null;
	/**
	 * 注入服务
	 *
	 * @var array
	 */
	private $injectServices = null;
	
	/**
	 * 模块配置
	 *
	 * @var array
	 */
	private $module = null;

	/**
	 * manifest path
	 *
	 * @param string $manifest        	
	 */
	public function __construct($manifest, $extends = array()) {
		is_string($manifest) && $manifest = Wind::getComponent('configParser')->parse($manifest);
		if ($extends) {
			$manifest = WindUtility::mergeArray($manifest, $extends);
			$charset = isset($manifest['application']['charset']) ? $manifest['application']['charset'] : 'utf-8';
		} else {
			$charset = Wekit::V('charset');
		}
		strncasecmp($charset, 'utf', 3) && $manifest = WindConvert::convert($manifest, $charset, 'utf-8');
		$this->manifest = $manifest;
	}

	/**
	 *
	 * @return field_type
	 */
	public function getManifest() {
		return $this->manifest;
	}

	/**
	 *
	 * @return multitype:
	 */
	public function getApplication($key = '', $default = '') {
		if ($this->application === null) {
			$this->application = $this->_resolvedConfig('application');
		}
		return $key ? (isset($this->application[$key]) ? $this->application[$key] : $default) : $this->application;
	}

	/**
	 *
	 * @return multitype:
	 */
	public function getInstallationService() {
		if ($this->installationService === null) {
			$this->installationService = $this->_resolvedConfig('installation-service');
			if (is_string($this->installationService)) {
				$this->installationService = explode(',', $this->installationService);
			}
		}
		return $this->installationService;
	}

	/**
	 *
	 * @return multitype:
	 */
	public function getHooks() {
		if ($this->hooks === null) {
			$this->hooks = array();
			$hooks = (array) $this->_resolvedConfig('hooks');
			foreach ($hooks as $key => $value) {
				$_d = empty($value['documents']) ? '' : $value['documents'];
				is_array($_d) && $_d = implode("\r\n", $_d);
				$this->hooks[$key] = array('name' => $key, 'document' => $_d);
			}
		}
		return $this->hooks;
	}

	/**
	 *
	 * @return multitype:
	 */
	public function getInjectServices() {
		if ($this->injectServices === null) {
			$this->injectServices = array();
			$injectServices = (array) $this->_resolvedConfig('inject-services');
			foreach ($injectServices as $key => $value) {
				if (!is_array($value)) continue;
				foreach ($value as $_k => $_v) {
					if (!is_array($_v)) continue;
					if (empty($_v['class'])) continue;
					$this->injectServices[] = array(
						'hook_name' => $key, 
						'alias' => 'ext_' . $_k, 
						'class' => $_v['class'], 
						'method' => (empty($_v['method']) ? '' : $_v['method']), 
						'loadway' => (empty($_v['loadway']) ? '' : $_v['loadway']), 
						'expression' => (empty($_v['expression']) ? '' : $_v['expression']),
						'description' => (empty($_v['description']) ? '' : $_v['description'])
						);
				}
			}
			
			$_tmp = array('class' => '', 'method' => '', 'loadway' => '', 'expression' => '');
		}
		return $this->injectServices;
	}

	/**
	 * 解析配置
	 *
	 * @param array|string $value        	
	 * @return array
	 */
	private function _resolvedConfig($key) {
		return empty($this->manifest[$key]) ? array() : $this->manifest[$key];
	}
}

?>