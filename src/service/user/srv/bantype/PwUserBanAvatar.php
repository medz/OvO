<?php
Wind::import('SRV:user.srv.bantype.PwUserBanTypeInterface');
Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 用户禁止类型-禁止头像 扩展
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserBanAvatar.php 22230 2012-12-19 21:45:20Z xiaoxia.xuxx $
 * @package src.service.user.srv.bantype
 */
class PwUserBanAvatar implements PwUserBanTypeInterface {
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::afterBan()
	 */
	public function afterBan(PwUserBanInfoDm $dm) {
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$info = $userDs->getUserByUid($dm->getField('uid'), PwUser::FETCH_MAIN);
		if (Pw::getstatus($info['status'], PwUser::STATUS_BAN_AVATAR)) return $info['status'];//已经禁止不需要再次更改
		$userDm = new PwUserInfoDm($dm->getField('uid'));
		$userDm->setBanAvatar(true);
		$userDs->editUser($userDm, PwUser::FETCH_MAIN);
		/* @var $userSrv PwUserService */
		$userSrv = Wekit::load('SRV:user.srv.PwUserService');
		$userSrv->restoreDefualtAvatar($dm->getField('uid'), 'ban');
		$p = 1 << (PwUser::STATUS_BAN_AVATAR - 1);
		return intval($info['status'] + $p);
	}
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::deleteBan()
	 */
	public function deleteBan($uid) {
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$info = $userDs->getUserByUid($uid, PwUser::FETCH_MAIN);
		if (!Pw::getstatus($info['status'], PwUser::STATUS_BAN_AVATAR)) return $info['status'];//已经解禁不需要再次更改
		
		$userDm = new PwUserInfoDm($uid);
		$userDm->setBanAvatar(false);
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$userDs->editUser($userDm, PwUser::FETCH_MAIN);
		/* @var $userSrv PwUserService */
		$userSrv = Wekit::load('SRV:user.srv.PwUserService');
		$userSrv->restoreDefualtAvatar($uid);
		$p = 1 << (PwUser::STATUS_BAN_AVATAR - 1);
		return intval($info['status'] - $p);
	}
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::getExtension()
	*/
	public function getExtension($fid) {
		return '全局';
	}
}