<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminAuth.php 24131 2013-01-22 05:55:40Z yishuo $
 * @package admin
 * @subpackage service
 */
class AdminAuth {

	/**
	 * 根据用户名查找后台用户
	 *
	 * @param string $username
	 * @return array
	 */
	public function findByUsername($username) {
		return $this->getAdminAuthDao()->findByUsername($username);
	}

	/**
	 * 根据用户ID查找用户是否是后台用户
	 *
	 * @param int $uid
	 * @return array
	 */
	public function findByUid($uid) {
		return $this->getAdminAuthDao()->findByUid($uid);
	}

	/**
	 * 根据ID查找后台用户
	 *
	 * @param int $id
	 * @return array
	 */
	public function findById($id) {
		return $this->getAdminAuthDao()->findById($id);
	}

	/**
	 * 分页查找后台用户
	 *
	 * @param int $page 当前页
	 * @param int $perPgae 每页显示条数
	 * @return array
	 */
	public function findByPage($page, $perPgae = 10) {
		$count = $this->getAdminAuthDao()->count();
		if (!$count) return array(0, array());
		$page = (int) $page;
		$countPage = ceil($count / $perPgae);
		$page = $page < 1 ? 1 : ($page > $countPage ? $countPage : $page);
		$list = $this->getAdminAuthDao()->find(($page - 1) * $perPgae, $perPgae);
		return array($count, $list, $page);
	}

	/**
	 * 删除后台用户
	 *
	 * @param id $id
	 * @return PwError|boolean
	 */
	public function del($id) {
		if (!$id) return new PwError('ADMIN:auth.del.fail');
		return $this->getAdminAuthDao()->del($id);
	}

	/**
	 * 编辑后台用户定义
	 *
	 * @param int $id
	 * @param array $roles
	 * @return array
	 */
	public function edit($id, $username, $roles) {
		if (!$id) return new PwError('ADMIN:auth.edit.fail.id.illegal');
		if (!$roles) return new PwError('ADMIN:auth.add.fail.role.empty');
		$fields['username'] = $username;
		$fields['roles'] = implode(',', (array) $roles);
		$fields['modified_time'] = time();
		$this->getAdminAuthDao()->updateById($id, $fields);
		return $fields;
	}

	/**
	 * 添加用户角色定义
	 *
	 * @param string $username
	 * @param array $roles
	 * @return array|PwError
	 */
	public function add($username, $uid, $roles) {
		if (empty($username)) return new PwError('ADMIN:auth.add.fail');
		if (empty($uid)) return new PwError('ADMIN:auth.add.fail');
		if (empty($roles)) return new PwError('ADMIN:auth.add.fail.role.empty');
		if ($this->getAdminAuthDao()->findByUsername($username)) {
			return new PwError('ADMIN:auth.add.fail.username.duplicate');
		}
		$fields['uid'] = $uid;
		$fields['username'] = $username;
		$fields['roles'] = implode(',', (array) $roles);
		$fields['created_time'] = time();
		$fields['modified_time'] = time();
		$this->getAdminAuthDao()->add($fields);
		return $fields;
	}

	/**
	 * @return AdminAuthDao
	 */
	private function getAdminAuthDao() {
		return Wekit::loadDao('ADMIN:service.dao.AdminAuthDao');
	}
}

?>