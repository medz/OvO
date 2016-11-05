<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户权限服务
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Nov 8, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwPermissionService.php 24736 2013-02-19 09:24:40Z jieyin $
 */

class PwPermissionService {
	
	private $_config;
	private $_configCategory;

	/**
	 * 获取一级菜单项
	 * 
	 * @param bool $manage (管理权限true|普通权限false)
	 * @return array
	 */
	public function getTopLevelCategories($manage = false){
		$permissionCategory = $this->getPermissionCategoryConfig();
		$topCategories = array();
		foreach ($permissionCategory as $k => $v) {
			$isMangage = isset($v['manage']) && $v['manage'] == true;
			if ($manage != $isMangage) continue;
			$topCategories[$k] = $v['name'];
		}
		return $topCategories;
	}
	
	public function getPermissionsByCategory($category) {
		$permissionCategory = $this->getPermissionCategoryConfig();
		return $permissionCategory[$category];
	}
	
	/**
	 * 获取某一组的所有权限点
	 * 
	 * @param string $category
	 */
	public function getPermissionKeysByCategory($category) {
		$permissionCategory = $this->getPermissionCategoryConfig();
		$config = $permissionCategory[$category];
		if (!$config['sub']) return array();
		$permissionKeys = array();		
		foreach ($config['sub'] as $v) {
			$permissionKeys = array_merge($permissionKeys, $v['items']);
		}
		return $permissionKeys;
	}
	
	public function getPermissionKeys($isManage = false){
		$permissionCategory = $this->getPermissionCategoryConfig();
		$permissionKeys = array();
		foreach ($permissionCategory as $k=>$config) {
			if ($isManage) {
				if (strpos($k, 'manage_') !== 0) continue;
			} else {
				if (strpos($k, 'manage_') === 0) continue;
			}
			if (!$config['sub']) continue;
			foreach ($config['sub'] as $v) {
				$permissionKeys = array_merge($permissionKeys, $v['items']);
			}
		}
		return $permissionKeys;
	}

	/**
	 * 获取权限配置设置
	 * 
	 * @param int $gid
	 * @param string $category
	 */
	public function getPermissionConfigByCategory($gid, $category) {
		//按分类获取权限点
		$permissions = $this->getPermissionsByCategory($category);
		if (!$permissions['sub']) return array();
		$permissionsKeys = $this->getPermissionKeysByCategory($category);
		$groupPermissions = $this->_getPermissionDs()->getPermissions($gid, $permissionsKeys);
		
		//获取权限配置
		$permissionConfigs = $this->getPermissionConfig();

		$configs = array();
		foreach ($permissions['sub'] as $k => $v) {
			if (!$v['items']) continue;
			$configs[$k] = array('name' => $v['name'], 'items' => array());
			foreach ($v['items'] as $v2) {
				$defaultValue = isset($groupPermissions[$v2]) ? $groupPermissions[$v2]['rvalue'] : null;
				$configs[$k]['items'][$v2] = array('config' => $permissionConfigs[$v2], 'default' => $defaultValue);
			}
		}
		return $configs;
	}
	
	/**
	 * 根据用户组获得用户组的的权限设置
	 *
	 * @param array $needKeys
	 * @param array $category
	 * @return array
	 */
	public function getPermissionPoint($needKeys , $category = array()) {
		if (!$category) {
			$categoryInfo = $this->getTopLevelCategories();
			$category = array_keys($categoryInfo);
		}
		$return = array();
		$permissionCategory = $this->getPermissionCategoryConfig();
		foreach ($category as $key) {
			if (!$permissionCategory[$key]['sub']) continue;
			$item = array();
			foreach ($permissionCategory[$key]['sub'] as $_sub => $_item) {
				if (!$_item['items']) continue;
				$_keys = $_item['items'];
				if ($needKeys && !($_keys = array_intersect($needKeys, $_item['items']))) continue;
				$item = array_merge($item, $_keys);
			}
			if (!$item) continue;
			$return[$key]['name'] = $permissionCategory[$key]['name'];
			$return[$key]['sub'] = $item;
		}
		return $return;
	}
	
	/**
	 * 根据用户组及需要的权限点获得该用户组的权限设置
	 *
	 * @param int $gid 用户组ID
	 * @param array $returnItems 需要返回的点
	 * @return array 
	 */
	public function getPermissionConfigByGid($gid, $returnItems) {
		$groupPermissions = $this->_getPermissionDs()->getPermissions($gid, $returnItems);
		$configs = array();
		//获取权限配置
		$permissionConfigs = $this->getPermissionConfig();
		foreach ($returnItems as $_i) {
			$description = null;
			$configs[$_i] = array('name' => $permissionConfigs[$_i][2]);
			$groupValue = isset($groupPermissions[$_i]) ? $groupPermissions[$_i]['rvalue'] : null;
			$type = $permissionConfigs[$_i][0];
			switch ($type) {
				case 'checkbox':
					$data = !empty($groupValue) ? $groupValue : array();
					if (in_array($_i, array('allow_thread_extend'))) {
						$data = array_keys($data);
					}
					foreach ($data as $_k => $_v) {
						$data[$_k] = isset($permissionConfigs[$_i][4][$_v]) ? $permissionConfigs[$_i][4][$_v] : $_v;
					}
					$data = implode("; ", $data);
					break;
				default:
					if (in_array($_i, array('allow_upload', 'allow_download'))) {
						$data = isset($permissionConfigs[$_i][4][$groupValue]) ? $permissionConfigs[$_i][4][$groupValue] : $groupValue;
						$type = 'html';
					} else {
						$data = $groupValue;
					}
					break;
			}
			$configs[$_i]['value'] = $data;
			$configs[$_i]['type'] = $type;
		}
		return $configs;
	}
	
	/**
	 * 获取权限菜单配置
	 *
	 * @return array
	 */
	public function getPermissionCategoryConfig() {
		if (!$this->_configCategory) {
			/* @var $_configParser WindConfigParser */
			$_configParser = Wind::getComponent('configParser');
			$file = Wind::getRealPath('SRV:usergroup.srv.permission.permissionCategory');
			$this->_configCategory = $_configParser->parse($file);
			$this->_configCategory = PwSimpleHook::getInstance('permissionCategoryConfig')->runWithFilters($this->_configCategory);
		}
		return $this->_configCategory;
	}
	
	/**
	 * 获取权限点配置
	 *
	 * @return array
	 */
	public function getPermissionConfig() {
		if (!$this->_config) {
			/* @var $_configParser WindConfigParser */
			$_configParser = Wind::getComponent('configParser');
			$file = Wind::getRealPath('SRV:usergroup.srv.permission.permissions');
			$this->_config = $_configParser->parse($file);
			$this->_config = PwSimpleHook::getInstance('permissionConfig')->runWithFilters($this->_config);
		}
		return $this->_config;
	}
	
	private function _getPermissionDs() {
		return Wekit::load('usergroup.PwUserPermission');
	}

	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
}