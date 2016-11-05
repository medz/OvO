<?php
Wind::import('ADMIN:service.srv.IAdminUserDependenceService');
Wind::import('WINDID:library.WindidError');

/**
 * 后台用户服务
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminUserDependenceService.php 24807 2013-02-21 09:24:27Z jieyin $
 * @package wind
 */
class AdminUserDependenceService implements IAdminUserDependenceService {
	
	public function getUserByUids($userids) {
		return $this->loadUser()->fetchUserByUid($userids);
	}

	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::updateUserStatus()
	*/
	public function updateUserStatus($uid, $status) {
		/*
		$userDs = $this->loadUser();
		$user = $userDs->getUserByUid($uid, PwUser::FETCH_MAIN);
		if ($user && (!Pw::getstatus($user['status'], PwUser::STATUS_ALLOW_LOGIN_ADMIN))) {
			Wind::import('SRV:user.dm.PwUserInfoDm');
			$dm = new PwUserInfoDm($uid);
			$dm->setAllowLoginAdmin($status);
			$userDs->editUser($dm, PwUser::FETCH_MAIN);
		}
		*/
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::verifyUser()
	 */
	public function verifyUser($username, $password) {
		$result = $this->loadUserService()->login($username, $password, 2);
		switch ($result[0]) {
			case 1://用户信息正常
				return $result[1];
			case -14://用户不存在
				return new PwError('USER:verify.error.name');
			case -13://用户密码错误
				return new PwError('USER:verify.error.pwd');
			case -20://用户安全问题错误
				return new PwError('USER:verify.error.question');
		}
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::getUserByUid()
	*/
	public function getUserByUid($userid) {
		return $this->loadUser()->getUserByUid($userid);
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::getUserByName()
	*/
	public function getUserByName($username) {
		$user = $this->loadUser()->getUserByName($username);
		return $user ? $user : array();
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::setUser()
	*/
	public function setUser($username, $password, $email, $groupid = 3, $uid = 0) {
		if (!$email) return new PwError('ADMIN:founder.edit.fail.email.empty');
		Wind::import('WSRV:user.dm.WindidUserDm');
		$userDm = new WindidUserDm($uid);
		$userDm->setEmail($email);
		//$userDm->setGroupid($groupid);
		$password && $userDm->setPassword($password);
		if (!$uid) {
			$userDm->setUsername($username);
			return $this->loadUser()->addUser($userDm);
		} else {
			return $this->loadUser()->editUser($userDm);
		}
	}

	/**
	 * @return PwUser
	 */
	private function loadUser() {
		return Wekit::load('WSRV:user.WindidUser');
	}

	/**
	 * 加载用户服务
	 * 
	 * @return PwUserService
	 */
	private function loadUserService() {
		return Wekit::load('WSRV:user.srv.WindidUserService');
	}
}
?>