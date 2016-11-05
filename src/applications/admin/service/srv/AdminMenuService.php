<?php
Wind::import('ADMIN:service.srv.helper.AdminMenuHelper');
/**
 * 后台菜单服务
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminMenuService.php 23734 2013-01-15 09:10:00Z jieyin $
 * @package admin
 * @subpackage service.srv
 */
class AdminMenuService {
	/**
	 * 菜单清单
	 *
	 * @var array
	 */
	protected $menuTable = null;

	/**
	 * 根据用户ID获取用户权限表
	 *
	 * 后台菜单存储方式分为三种:<code>
	 * 1. conf/mainmenu.php, 可以将菜单直接写入mainmenu.php
	 * 2. 扩展菜单,例如扩展菜单文件为'menu1.php',则将该扩展源写入mainmenu.php 的 '_extensions' 字段.
	 * 如:
	 * '_extensions' => array('test1' => array('resource' =>
	 * 'ADMIN:conf.menu1.php')));
	 * 扩展外部的菜单位置没有限制,只要将扩展菜单路径以命名空间的方式放入到'resource'下即可.
	 * 3. 用数据库扩展.
	 * </code>
	 * 
	 * @example <code>
	 *          Array('admin' => Array(
	 *          'id' => admin,
	 *          'name' => admin,
	 *          'icon' => '',
	 *          'tip' => '',
	 *          'items' => Array('admin_install' => Array(...),'admin_auth' =>
	 *          Array(...)))
	 *          )
	 *          </code>
	 * @param int $uid        	
	 * @return array
	 */
	public function getMyMenus($dm) {
		/* @var $userService AdminUserService */
		$userService = Wekit::load('ADMIN:service.srv.AdminUserService');
		$myMenus = $userService->getAuths($dm);
		$menuTables = $this->getMenuTable();
		if ($myMenus !== '-1') {
			foreach ($menuTables as $key => $value)
				if (isset($value['url']) && !in_array($key, (array) $myMenus)) unset(
					$menuTables[$key]);
		}
		$menus = AdminMenuHelper::resolveMenuStruct($menuTables);
		foreach ($menus as $key => $value) {
			if (isset($value['items']) && empty($value['items'])) {
				unset($menus[$key]);
			}
		}
		return $menus;
	}

	/**
	 * 获取我的常用菜单
	 *
	 * @param AdminUserBo $user
	 * @return array
	 */
	public function getCustomMenus(AdminUserBo $user) {
		$menuTables = $this->getMenuTable();
		/* @var $adminCustom AdminCustom */
		$adminCustom = Wekit::load('ADMIN:service.AdminCustom');
		$r = $adminCustom->findByUsername($user->username);
		$myMenus = $r ? explode(',', $r['custom']) : array();
		$return = array();
		foreach ($menuTables as $key => $value) {
			if (isset($value['url']) && in_array($key, $myMenus)) {
				$return[$key] = $value;
			}
		}
		return $return;
	}

	/**
	 * 获得菜单数据table,该菜单节点table并不展示节点间的层级关系.
	 *
	 * @return multitype:
	 */
	public function getMenuTable() {
		if (!$menuTables = $this->_getMenuTable()) return array();
		unset($menuTables['__auths']);
		return $menuTables;
	}

	/**
	 * 获得当前的菜单权限结构信息表
	 *
	 * @example <code>
	 *          //'default' 当权限的m设置为空时则为'default'.
	 *          'default' => Array(
	 *          'install' => Array('run' => admin_install),
	 *          'auth' => Array('_all' => admin_auth))
	 *          </code>
	 * @return array
	 */
	public function getMenuAuthStruts() {
		if (!$menusInfo = $this->_getMenuTable()) return array();
		return isset($menusInfo['__auths']) ? $menusInfo['__auths'] : array();
	}

	/**
	 * 获得菜单数据table,该菜单节点table并不展示节点间的层级关系.
	 *
	 * 该方法解析所有扩展菜单表或者扩展菜单配置文件,并将菜单合并为一份完整的菜单table并返回.
	 * 
	 * @example <code> 节点列表'admin' => array('admin', array()),
	 *          'admin_install' => array('应用菜单安装', 'install/run', '', '',
	 *          'admin'),
	 *          'admin_auth' => array('菜单权限', 'auth/*', '', '', 'admin'),</code>
	 * @return array
	 */
	private function _getMenuTable() {
		if ($this->menuTable === null) {
			
			/* @var $_configParser WindConfigParser */
			$_configParser = Wind::getComponent('configParser');
			// 'ADMIN:conf.mainmenu.php'
			$mainMenuConfFile = Wind::getRealPath(Wekit::app()->menuPath, true);
			$menus = $_configParser->parse($mainMenuConfFile);
			
			/* extend menus by file */
			if (isset($menus['_extensions'])) {
				$_extensions = $menus['_extensions'];
				foreach ($_extensions as $_extName => $_ext) {
					if (!isset($_ext['resource'])) continue;
					$_tmp = Wind::getRealPath($_ext['resource'], true);
					$cacheKey .= filemtime($_tmp);
					$_extensions[$_extName]['resource'] = $_tmp;
				}
				unset($menus['_extensions']);
			} else
				$_extensions = array();
			
			$menus = PwSimpleHook::getInstance('admin_menu')->runWithFilters($menus);
			
			foreach ($_extensions as $key => $value) {
				if (!isset($value['resource'])) continue;
				$_tmp = $_configParser->parse($value['resource']);
				$menus = WindUtility::mergeArray($menus, $_tmp);
			}
			AdminMenuHelper::verifyMenuConfig($menus, $menus, $this->menuTable);
		}
		return $this->menuTable;
	}
}

?>