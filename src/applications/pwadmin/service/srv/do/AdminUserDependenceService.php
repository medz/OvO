<?php
Wind::import('ADMIN:service.srv.IAdminUserDependenceService');

/**
 * 后台用户服务
 * 
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
 */
class AdminUserDependenceService implements IAdminUserDependenceService {
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::getUserByUids()
	 */
	public function getUserByUids($userids) {
		return $this->loadUser()->fetchUserByUid($userids);
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::updateUserStatus()
	*/
	public function updateUserStatus($uid, $status) {
		$userDs = $this->loadUser();
		$user = $userDs->getUserByUid($uid, PwUser::FETCH_MAIN);
		if ($user && (!Pw::getstatus($user['status'], PwUser::STATUS_ALLOW_LOGIN_ADMIN))) {
			Wind::import('SRV:user.dm.PwUserInfoDm');
			$dm = new PwUserInfoDm($uid);
			$dm->setAllowLoginAdmin($status);
			$userDs->editUser($dm, PwUser::FETCH_MAIN);
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserDependenceService::verifyUser()
	 */
	public function verifyUser($username, $password) {
		return $this->loadUserService()->verifyUser($username, $password, 2);
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
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$userDm = new PwUserInfoDm($uid);
		$userDm->setEmail($email);
		$userDm->setGroupid($groupid);
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
		return Wekit::load('user.PwUser');
	}

	/**
	 * 加载用户服务
	 * 
	 * @return PwUserService
	 */
	private function loadUserService() {
		return Wekit::load('user.srv.PwUserService');
	}
}
?>