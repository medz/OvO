<?php

/**
 * 后台用户的业务对象
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: AdminUserBo.php 23734 2013-01-15 09:10:00Z jieyin $
 * @package src.service.user.bo
 */
class AdminUserBo {
	
	public $uid;
	public $username;
	public $gid;
	public $ip;

	public function __construct($user) {
		$this->info = $user;
		if ($this->info) {
			$this->uid = $this->info['uid'];
			$this->username = $this->info['username'];
			$this->gid = ($this->info['groupid'] == 0) ? $this->info['memberid'] : $this->info['groupid'];
		} else {
			$this->reset();
		}
	}

	public function isExists() {
		return $this->gid != 2;
	}

	public function reset() {
		$this->uid = 0;
		$this->gid = 2;
		$this->username = '游客';
		$this->info = array(
			'lastpost' => Pw::getCookie('guest_lastpost')
		);
	}
}

?>