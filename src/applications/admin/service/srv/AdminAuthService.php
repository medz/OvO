<?php
/**
 * 后台用户管理服务
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminAuthService.php 24805 2013-02-21 09:14:12Z jieyin $
 * @package admin
 * @subpackage service.srv
 */
class AdminAuthService {

	/**
	 * 删除后台用户
	 *
	 * @param int $id
	 * @return int|PwError
	 */
	public function del($id) {
		/* @var $authDs AdminAuth */
		$authDs = Wekit::load('ADMIN:service.AdminAuth');
		$info = $authDs->findById($id);
		if (!$info) return new PwError('ADMIN:auth.del.fail');
		$this->getAdminUserService()->loadUserService()->updateUserStatus($info['uid'], false);
		return $authDs->del($id);
	}

	/**
	 * 添加用户角色定义
	 *
	 * @param string $username
	 * @param array $roles
	 * @return array|PwError
	 */
	public function add($username, $roles) {
		if (empty($username)) return new PwError('ADMIN:auth.add.fail.username.empty');
		if (empty($roles)) return new PwError('ADMIN:auth.add.fail.role.empty');
		
		$userInfo = $this->getAdminUserService()->verifyUserByUsername($username);
		if (!$userInfo) return new PwError('ADMIN:auth.add.fail.username.exist');
		
		/* @var $authService AdminAuth */
		$authService = Wekit::load('ADMIN:service.AdminAuth');
		$result = $authService->add($username, $userInfo['uid'], $roles);
		if ($result instanceof PwError) return $result;
		$this->getAdminUserService()->loadUserService()->updateUserStatus($userInfo['uid'], true);
		return $result;
	}

	/**
	 * 编辑用户权限
	 *
	 * @param int $id
	 * @param array $roles
	 */
	public function edit($id, $roles) {
		if (!$id) return new PwError('ADMIN:auth.edit.fail.id.illegal');
		if (!$roles) return new PwError('ADMIN:auth.add.fail.role.empty');
		$auth = $this->_loadAuthService()->findById($id);
		if (empty($auth)) return new PwError('ADMIN:auth.edit.fail');
		$user = $this->getAdminUserService()->getUserByUids($auth['uid']);
		if (empty($user)) return new PwError('ADMIN:auth.edit.fail.user.exist');
		return $this->_loadAuthService()->edit($id, $user['username'], $roles);
	}

	/**
	 * 获取用户权限列表
	 *
	 * @param int $page
	 * @param int $perpage
	 * @return array|PwError
	 */
	public function fetchByPage($page, $perpage) {
		list($count, $list, $page) = $this->_loadAuthService()->findByPage($page, $perpage);
		$uids = Pw::collectByKey($list, 'uid');
		$users = $this->getAdminUserService()->getUserByUids($uids);
		foreach ($list as $key => $value) {
			if (isset($users[$value['uid']])) {
				$value['username'] = $users[$value['uid']]['username'];
			}
			$list[$key] = $value;
		}
		return array($count, $list, $page);
	}

	/**
	 * @return AdminAuth
	 */
	private function _loadAuthService() {
		return Wekit::load('ADMIN:service.AdminAuth');
	}

	/**
	 * @return AdminUserService
	 */
	private function getAdminUserService() {
		return Wekit::load('ADMIN:service.srv.AdminUserService');
	}

	/**
	 * @return AdminAuthDao
	 */
	private function getAdminAuthDao() {
		return Wekit::loadDao('ADMIN:service.dao.AdminAuthDao');
	}
}

?>