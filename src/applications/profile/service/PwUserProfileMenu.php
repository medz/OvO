<?php

/**
 * 个人中心-扩展菜单服务
 * 
 * hook：
 * s_profile_menus
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserProfileMenu.php 21352 2012-12-05 06:48:57Z xiaoxia.xuxx $
 * @package src.products.u.service
 */
class PwUserProfileMenu {
	private $menuConfig = null;
	private $cacheKey = 'u_profile_menus';
	private $menuFile = 'APPS:profile.conf.profilemenu.php';
	private $extend = array();
	private $first = null;
	
	/**
	 * 获取左侧菜单扩展的菜单项
	 * 
	 * @return array
	 */
	public function getMenus() {
		return $this->registerMenus();
	}
	/**
	 * 
	 */
	public function getFirstMenu() {
		return $this->first;
	}
	
	/**
	 * 获得某个菜单项的tabs
	 *
	 * @param string $left
	 * @return array
	 */
	public function getTabs($left) {
		$menus = $this->registerMenus();
		return $menus[$left]['tabs'];
	}
	
	/**
	 * 获得当前的菜单项
	 *
	 * @param string $left
	 * @param string $tab
	 * @return string
	 */
	public function getCurrentTab($left, $tab = '') {
		$menus = $this->registerMenus();
		if (!isset($menus[$left])) {
			$left = $this->getFirstMenu();
		}
		$currentLeft = $menus[$left];
		if (empty($currentLeft['tabs'])) return array($left, '');
		if (!$tab && !isset($currentLeft[$tab])) {
			$_temp = array_keys($currentLeft['tabs']);
			$tab = $_temp[0];
		}
		return array($left, $tab);
	}

	/**
	 * 获得菜单列表
	 *
	 * @return array
	 */
	private function registerMenus() {
		if (!empty($this->extend)) return $this->extend;
		$menus = $this->_getFromFile();
		$menus = PwSimpleHook::getInstance('profile_menus')->runWithFilters($menus);
		$_menus = array();
		foreach ($menus['profile_left'] as $key => $value) {
			(null == $this->first) && $this->first = $key;
			if (isset($value['tabs']) && $value['tabs'] && isset($menus[$key . '_tabs'])) {
				$value['tabs'] =  $menus[$key . '_tabs'];
			} else {
				$value['tabs'] = array();
			}
			$_menus[$key] = $value;
		}
		$this->extend = $_menus;
		return $this->extend;
	}
	
	/**
	 * 获得缺省的设置页
	 * 
	 * @return array
	 */
	private function _getFromFile() {
		return include(Wind::getRealPath($this->menuFile, true));
	}
}