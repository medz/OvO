<?php

/**
 * 添加用户-添加用户相关用户组
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserDoBelong.php 8620 2012-04-21 09:48:09Z xiaoxia.xuxx $
 * @package src.hooks.PwAddUser
 */
class PwUserDoBelong {
	
	/**
	 * 编辑用户信息
	 *
	 * @param PwUserInfoDm $dm
	 * @return boolean|PwError
	 */
	public function editUser($dm) {
		$belongs = $dm->getUserBelongs();
		if ($belongs === null) {
			return true;
		}
		/* @var $belongDs PwUserBelong */
		$belongDs = Wekit::load('user.PwUserBelong');
		if (!$belongs)  {
			return $belongDs->deleteByUid($dm->uid);
		}
		return $belongDs->update($dm->uid, $belongs);
	}
	
	/**
	 * 根据用户ID删除信息
	 *
	 * @param int $uid 用户ID
	 * @return boolean|PwError
	 */
	public function deleteUser($uid) {
		/* @var $belongDs PwUserBelong */
		$belongDs = Wekit::load('user.PwUserBelong');
		return $belongDs->deleteByUid($uid);
	}
	
	/**
	 * 根据用户ID列表批量删除用户数据
	 *
	 * @param array $uids
	 * @return boolean|PwError
	 */
	public function batchDeleteUser($uids) {
		/* @var $belongDs PwUserBelong */
		$belongDs = Wekit::load('user.PwUserBelong');
		return $belongDs->batchDeleteByUids($uids);
	}
}