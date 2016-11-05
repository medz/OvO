<?php
Wind::import('ADMIN:service.bo.IAdminUserBo');
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class AdminDefaultUserBo implements IAdminUserBo {
	public $uid;
	public $username;
	public $gid;
	public $password;

	public function __construct($userinfo = array()) {
		if ($userinfo) {
			$this->uid = $userinfo['uid'];
			$this->username = $userinfo['username'];
			$this->gid = $userinfo['gid'];
			$this->password = $userinfo['password'];
		}
	}

	public function reset() {
		$this->uid = $this->username = $this->gid = $this->password = null;
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserBo::getUid()
	 */
	public function getUid() {
		return $this->uid;
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserBo::getUsername()
	 */
	public function getUsername() {
		return $this->username;
	}
	
	/* (non-PHPdoc)
	 * @see IAdminUserBo::isExists()
	 */
	public function isExists() {
		return $this->gid != 2;
	}
}

?>