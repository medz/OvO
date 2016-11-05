<?php
Wind::import('SRV:user.srv.logout.PwLogoutDoBase');

/**
 * 为了用户可以及时的更新在线状态，用户退出之前更新用户的最后访问时间
 * 用户退出  更新用户最后的访问时间
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogoutDoUpdateLastvisit.php 18618 2012-09-24 09:31:00Z jieyin $
 * @package src.service.user.srv.logout.do
 */
class PwLogoutDoUpdateLastvisit extends PwLogoutDoBase {
	
	/* (non-PHPdoc)
	 * @see PwLogoutDoBase::beforeLogout()
	 */
	public function beforeLogout(PwUserBo $bo) {
		if (!$bo->isExists()) return true;
		$onlineTime = intval(Wekit::C('site', 'onlinetime'));
		if ($onlineTime <= 0) return true;
		$newLastVisit = $bo->info['lastvisit'] - ($onlineTime * 60);
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$dm = new PwUserInfoDm($bo->uid);
		$dm->setLastvisit($newLastVisit);
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$userDs->editUser($dm, PwUser::FETCH_DATA);
		return true;
	}
}