<?php
/**
 * 菜单处理帮助类
 * 
 * 主要职责:<code>
 * 1. parseMenuConfig, 解析菜单配置
 * </code>
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminMenuHelper.php 23424 2013-01-09 09:31:45Z xiaoxia.xuxx $
 * @package admin
 * @subpackage library
 */
class AdminMenuHelper {

	/**
	 * 重新设置menus的结构,将menus的结构设置为一级节点以及所以子节点模式
	 *  
	 * 如果含有三级菜单，则将三级菜单合并到一级菜单中，同时二级菜单不要 
	 * @example <code>
  	 * 一级菜单
  	 * config: parent=>'root'
  	 *  二级菜单：
  	 *  config1: parent => 'config'
  	 *  config2: parent => 'config'
  	 * 三级菜单：
  	 * config3: parent => 'config1'
  	 * config4: parent => 'config1'
  	 * 
  	 * 则最后出来的结果为：
  	 * config的items格式为:
  	 *  array('config3' => array(), 'config4' => array(), 'config2' => array());
	 * </code>
	 * @param array $menus
	 * @return array
	 */
	static public function resetMenuStruts($menus) {
		$_menus = array();
		foreach ($menus as $key => $value) {
			if ($key == '__auths') continue;
			if ('root' == $value['parent']) {
				$_menus[$key] = $menus[$key];
			} elseif (isset($value['url'])) {
				$parentKey = $value['parent'];
				//如果当前菜单是三级菜单，则将三级菜单合并到二级菜单中
				if ('root' != $menus[$parentKey]['parent']) {
					$parentKey = $menus[$parentKey]['parent'];
				}
				isset($_menus[$parentKey]) || $_menus[$parentKey] = $menus[$parentKey];
				$_menus[$parentKey]['items'][] = $value;
			}
		}
		return $_menus;
	}

	/**
	 * 递归的方式调用,将复合的节点设置合并为单节点设置方式
	 * 
	 * @param array $menus
	 * @param array $allMenus 处理结果集,引用方式传递
	 * @param array $_menus 处理结果集,引用方式传递
	 * @param string $pNode 默认为'',父节点key值
	 * @return array
	 */
	static public function verifyMenuConfig($menus, $allMenus, &$_menus = array(), $pNode = 'root') {
		isset($_menus['__auths']) || $_menus['__auths'] = array();
		foreach ($menus as $key => $menu) {
			if (is_string($menu) && isset($allMenus[$menu])) {
				$key = $menu;
				$menu = $allMenus[$menu];
			}
			if (isset($_menus[$key])) continue;
			if (!is_array($menu) && count($menu) < 2) continue;
			if (is_array($menu[1])) {
				self::verifyMenuConfig($menu[1], $allMenus, $_menus, $key);
				$menu[1] = array();
			}
			if ($pNode && empty($menu[4])) $menu[4] = $pNode;
			$_tmp = array(
				'id' => $key, 
				'name' => $menu[0], 
				'icon' => isset($menu[2]) ? $menu[2] : '', 
				'tip' => isset($menu[3]) ? $menu[3] : '', 
				'parent' => $menu[4], 
				'top' => isset($menu[5]) ? $menu[5] : '');
			if (is_array($menu[1]))
				$_tmp['items'] = $menu[1];
			else {
				$_tmp['url'] = $menu[1];
				self::_resolveMenuAuth($key, $_tmp, $_menus['__auths']);
			}
			$_menus[$key] = $_tmp;
			$tNode = $key;
		}
	}

	/**
	 * 解析菜单配置
	 * 
	 * 将菜单配置解析为需要的格式并返回<code>
	 * 一个菜单个配置格式中包含: 菜单名称, 路由信息, 菜单图标, 菜单tip, 父节点, 上一个菜单
	 * 'key' => array('菜单名称', '应用路由', 'icon' , ' tip' ,'', '上一个菜单key'),
	 * 'key1' => array('菜单名称', '应用路由', 'icon' , ' tip' ,'key', ''),
	 * 'key2' => array('菜单名称', '应用路由', 'icon' , ' tip' ,'key', 'key1'),
	 * 将上述菜单按照结构化的方式解析并返回.
	 * key => array('key1','key2');
	 * </code>
	 * @param array $menus 原始菜单
	 * @return array
	 */
	static public function resolveMenuStruct($menus) {
		isset($menus['root']) || $menus['root']['items'] = array();
		foreach ($menus as $key => $_node) {
			if ($key === 'root') continue;
			$_tmp = isset($_node['parent']) ? $_node['parent'] : 'root';
			if (isset($menus[$_tmp])) {
				if (!isset($menus[$_tmp]['items'])) continue;
				$menus[$_tmp]['items'][$key] = &$menus[$key];
			} else {
				$menus['root']['items'][$key] = &$menus[$key];
			}
		}
		return self::_parseMenuTops($menus['root']['items']);
	}

	/**
	 * 解析菜单上下层节点关系
	 * 
	 * 在此方法中只处理本层的上下层节点关系,如果某个节点的上个节点在本层中不存在,则默认按照书写顺写.
	 * @param array $menus
	 * @return array
	 */
	static private function _parseMenuTops($menus) {
		$tmp = array();
		foreach ((array) $menus as $key => $value) {
			if (array_key_exists($key, $tmp)) continue;
			if (isset($value['items'])) {
				$value['items'] = self::_parseMenuTops($value['items']);
			}
			$top = $value['top'];
			if (!array_key_exists($top, $menus)) {
				$tmp[$key] = $value;
			} elseif (!array_key_exists($top, $tmp)) {
				$tmp[$top] = $menus[$top];
				$tmp[$key] = $value;
			} else {
				$_tmp = array();
				foreach ($tmp as $_k => $_v) {
					if ($_k === $top) $_tmp[$key] = $value;
					$_tmp[$_k] = $_v;
				}
				$tmp = $_tmp;
			}
		}
		return $tmp;
	}

	/**
	 * 获取菜单内容,并解析菜单权限信息
	 * 
	 * @param string $key
	 * @param array $menu
	 * @param array $menus
	 */
	static private function _resolveMenuAuth($key, &$menu, &$menus) {
		if (!isset($menu['url'])) return;
		$action = $menu['url'];
		list($_action, $_arg) = explode('?', $action . '?');
		$_action = explode('/', trim($_action, '/') . '/');
		end($_action);
		if (!$_aAuth = prev($_action)) return;
		if (!$_cAuth = prev($_action)) return;
		$_mAuth = prev($_action);
		$_aAuth = explode(',', $_aAuth);
		$_cAuth = explode(',', $_cAuth);
		if ($_mAuth) {
			$_mAuth = explode(',', $_mAuth);
			$_url = $_mAuth[0];
		} else {
			$_mAuth = array('default');
			$_url = '';
		}
		
		$_a = '';
		if (in_array('*', $_aAuth)) {
			foreach ($_aAuth as $a) {
				if ($a === '*') continue;
				$_a = $a;
				break;
			}
			$_aAuth = array();
		}
		$_a || $_a = isset($_aAuth[0]) ? $_aAuth[0] : 'run';
		$menu['url'] = WindUrlHelper::createUrl($_url . '/' . $_cAuth[0] . '/' . $_a . ($_arg ? '/?' . $_arg : ''));
		
		isset($_mAuth[0]) || $_mAuth[0] = 'default';
		foreach ($_mAuth as $_m) {
			isset($menus[$_m]) || $menus[$_m] = array();
			foreach ($_cAuth as $_cv) {
				isset($menus[$_m][$_cv]) || $menus[$_m][$_cv] = array();
				if (empty($_aAuth)) {
					isset($menus[$_m][$_cv]['_all']) || $menus[$_m][$_cv]['_all'] = array();
					$menus[$_m][$_cv]['_all'][] = $key;
				} else {
					foreach ($_aAuth as $_av) {
						isset($menus[$_m][$_cv][$_av]) || $menus[$_m][$_cv][$_av] = array();
						$menus[$_m][$_cv][$_av][] = $key;
					}
				}
			}
		}
	}
}

?>