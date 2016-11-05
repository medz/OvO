<?php
/**
 * 后台角色管理服务
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminRole.php 24240 2013-01-23 07:09:33Z yishuo $
 * @package wind
 */
class AdminRole {

	/**
	 * 删除权限组
	 * 
	 * @param int $rid 权限组ID
	 */
	public function delById($rid) {
		if (empty($rid)) return new PwError('ADMIN:role.del.fail.id.empty');
		$result = $this->getAdminRoleDao()->del($rid);
		if (!$result) return new PwError('ADMIN:role.del.fail');
		return true;
	}

	/**
	 * 添加后台管理角色
	 *
	 * @param string $name 角色名称
	 * @param array $auths 角色权限
	 * @return boolean|PwError
	 */
	public function addRole($name, $auths) {
		if (empty($name)) return new PwError('ADMIN:role.add.fail.name.empty');
		if ($this->getAdminRoleDao()->findByName($name)) {
			return new PwError('ADMIN:role.add.fail.name.exist');
		}
		$fields['name'] = $name;
		$fields['auths'] = implode(',', (array) $auths);
		$fields['created_time'] = time();
		$fields['modified_time'] = time();
		$result = $this->getAdminRoleDao()->add($fields);
		if (!$result) return new PwError('ADMIN:role.add.fail');
		return true;
	}

	/**
	 * 编辑后台管理角色
	 *
	 * @param id $id
	 * @param string $name
	 * @param array $auths
	 */
	public function editRole($id, $name, $auths) {
		$fields['name'] = $name;
		$fields['auths'] = implode(',', (array) $auths);
		$fields['modified_time'] = time();
		$_roles = $this->findRolesByNames(array($name));
		foreach ($_roles as $key => $value) {
			if ($value['id'] !== $id) return new PwError('ADMIN:role.add.fail.name.exist');
		}
		return $this->getAdminRoleDao()->updateById($id, $fields);
	}

	/**
	 * 查找已存在的全部角色定义
	 */
	public function findRoles() {
		return $this->getAdminRoleDao()->find(0, 100);
	}

	/**
	 * 根据角色名称查找角色列表
	 *
	 * @param array $names
	 * @return array|PwError
	 */
	public function findRolesByNames($names) {
		$result = $this->getAdminRoleDao()->findByNames($names);
		if (!$result) return new PwError('ADMIN:role.find.fail');
		return $result;
	}

	/**
	 * 根据角色名称查找角色列表
	 *
	 * @param array $names
	 * @return array|PwError
	 */
	public function findRolesByName($name) {
		$result = $this->getAdminRoleDao()->findByName($name);
		if (!$result) return new PwError('ADMIN:role.find.fail');
		return $result;
	}

	/**
	 * 根据角色ID查找角色
	 *
	 * @param array $ids
	 * @return array|PwError
	 */
	public function findRolesByIds($ids) {
		$result = $this->getAdminRoleDao()->findByIds($ids);
		if (!$result) return new PwError('ADMIN:role.find.fail');
		return $result;
	}

	/**
	 * 根据角色ID查找角色
	 *
	 * @param int $id
	 * @return array
	 */
	public function findRoleById($id) {
		$result = $this->getAdminRoleDao()->findById($id);
		if (!$result) return new PwError('ADMIN:role.find.fail');
		return $result;
	}

	/**
	 * @return AdminRoleDao
	 */
	private function getAdminRoleDao() {
		return Wekit::loadDao('ADMIN:service.dao.AdminRoleDao');
	}
}

?>