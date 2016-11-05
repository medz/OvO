<?php
/**
 * 删除用户同时删除用户的相关禁止信息
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserDoBan.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.hooks.PwDeleteUser
 */
class PwUserDoBan {
	
	/**
	 * 根据用户ID删除用户信息
	 *
	 * @param int $uid
	 * @return boolean|PwError
	 */
	public function deleteBan($uid) {
		/* @var $banDs PwUserBan */
		$banDs = Wekit::load('user.PwUserBan');
		return $banDs->deleteByUid($uid);
	}
	
	/**
	 * 根据用户ID列表批量删除用户数据
	 *
	 * @param array $uids
	 * @return bolean|PwError
	 */
	public function batchDeleteBan($uids) {
		/* @var $banDs PwUserBan */
		$banDs = Wekit::load('user.PwUserBan');
		return $banDs->batchDeleteByUids($uids);
	}
}