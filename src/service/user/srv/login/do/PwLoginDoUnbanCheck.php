<?php
Wind::import('SRV:user.srv.login.PwUserLoginDoBase');

/**
 * 登录用户自动解除禁止的检查
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLoginDoUnbanCheck.php 23904 2013-01-17 05:27:48Z xiaoxia.xuxx $
 * @package service.user.srv.login.do
 */
class PwLoginDoUnbanCheck extends PwUserLoginDoBase {
	
	/* (non-PHPdoc)
	 * @see PwUserLoginDoBase::welcome()
	 */
	public function welcome(PwUserBo $userBo, $ip) {
		Wind::import('SRV:user.srv.PwBanBp');
		$banBp = new PwBanBp($userBo->uid);
		if ($banBp->checkIfBan()) {
			if (Pw::getstatus($userBo->info['status'], PwUser::STATUS_BAN_AVATAR) && false === $banBp->checkIfBanAvatar()) {
				$banBp->recoveryBanAvatarError();
			} else {
				$banBp->endDateTimeBanAvatar();
			}
			if (Pw::getstatus($userBo->info['status'], PwUser::STATUS_BAN_SIGN) && false === $banBp->checkIfBanSign()) {
				$banBp->recoveryBanSignError();
			} else {
				$banBp->endDateTimeBanSign();
			}
			if ($userBo->gid == 6 && false == $banBp->checkIfBanSpeak()) {
				$banBp->recoveryBanSpeaKError();
			} else {
				$banBp->endDateTimeBanSpeak();
			}
			$userBo->info = array_merge($userBo->info, Wekit::load('user.PwUser')->getUserByUid($userBo->uid));
			$userBo->gid = ($userBo->info['groupid'] == 0) ? $userBo->info['memberid'] : $userBo->info['groupid'];
			if ($userBo->info['groups']) $userBo->groups = explode(',', $userBo->info['groups']);
			$userBo->groups[] = $this->gid;
		}
		return $userBo;
	}
}