<?php
Wind::import('SRV:user.srv.login.PwUserLoginDoBase');
Wind::import('SRV:task.srv.PwTaskApply');
/**
 * 当前第一次登录DO
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.service.task.srv.condition
 */
class PwUserLoginDoBelong extends PwUserLoginDoBase {
	
	/* (non-PHPdoc)
	 * @see PwUserLoginDoBase::welcome()
	 */
	public function welcome(PwUserBo $userBo, $ip) {
		if (0 != $userBo->info['groupid'] || $userBo->info['groups']) {
			/* @var $_userBelong PwUserBelong */
			$_userBelong = Wekit::load('user.PwUserBelong');
			$belongs = $_userBelong->getUserBelongs($userBo->uid);
			$_groups = array();
			$gid = 0;
			foreach ($belongs as $_gid => $_item) {
				$_groups[$_gid] = $_item['endtime'];
			}
			if ($_groups) {
				/* @var $userService PwUserService */
				$userService = Wekit::load('user.srv.PwUserService');
				list($gid, $_groups) = $userService->caculateUserGroupid($userBo->info['groupid'], $_groups);
			}
			$dm = new PwUserInfoDm($userBo->uid);
			$dm->setGroupid($gid)->setGroups($_groups);
			Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_MAIN);
		}
		return true;
	}
}