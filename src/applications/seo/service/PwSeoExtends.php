<?php
/**
 * seo扩展
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package modules.seo.service
 */
class PwSeoExtends {
	
	private $config = null;
	private $codes = array();
	
	/**
	 * 获取seo配置
	 *
	 * @return array
	 */
	public function getConfig() {
		if ($this->config !== null) return $this->config;
		$this->config = @include(Wind::getRealPath('APPS:seo.conf.seoExtends'));
		$this->config = PwSimpleHook::getInstance('seo_config')->runWithFilters($this->config);
		return $this->config;
	}
	
	/**
	 * 获取菜单
	 *
	 * @return array  
	 */
	public function getTabs() {
		if($this->config === null) $this->getConfig();
		$tabs = array();
		foreach ($this->config as $k => $v) {
			$tabs[$k] = array('title' => $v['title'], 'url' => $v['url'], 'current' => '');
		}
		return $tabs;
	}
	
	/**
	 * 获得模式下的所有页面
	 * 
	 * @param string $mod
	 * @return array
	 */
	public function getPages($mode) {
		if($this->config === null) $this->getConfig();
		$pages = array();
		foreach ($this->config[$mode]['page'] as $k => $v) {
			$pages[$k] = $v['title'];
		}
		return $pages;
	}
	
	/**
	 * 根据模式获取可使用占位符
	 *
	 * @param string $page
	 * @param string $mode
	 * @return array 
	 */
	public function getCodes($mode) {
		if($this->config === null) $this->getConfig();
		$codes = array();
		foreach ($this->config[$mode]['page'] as $k => $v) {
			$codes[$k] = $v['code'];
		}
		return $codes;
	}
	
	/**
	 * 获取某个页面的默认seo数据
	 *
	 * @param string $page
	 * @param string $mode
	 * @return array
	 */
	public function getDefaultSeoByPage($page, $mode) {
		if($this->config === null) $this->getConfig();
		return isset($this->config[$mode]['page'][$page]['default']) ? $this->config[$mode]['page'][$page]['default'] : array();
	}

}

?>