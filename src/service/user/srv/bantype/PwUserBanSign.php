<?php
Wind::import('SRV:user.srv.bantype.PwUserBanTypeInterface');
Wind::import('SRV:user.dm.PwUserInfoDm');
/**
 * 禁止签名类型
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserBanSign.php 22230 2012-12-19 21:45:20Z xiaoxia.xuxx $
 * @package src.service.user.srv.bantype
 */
class PwUserBanSign implements PwUserBanTypeInterface {
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::afterBan()
	 */
	public function afterBan(PwUserBanInfoDm $dm) {
		//禁止签名，用户的签名不清空还是保留 2012-10-25
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$info = $userDs->getUserByUid($dm->getField('uid'), PwUser::FETCH_MAIN);
		if (Pw::getstatus($info['status'], PwUser::STATUS_BAN_SIGN)) return $info['status'];//已经禁止不需要再次更改

		//$end_time = $dm->getField('end_time') > 0 ? Pw::time2str($dm->getField('end_time'), 'Y年m月d日 H:i') : '永久';
		//构建显示禁止签名的文本内容
	//	$newSign = sprintf('由于"%s"被%s禁止签名至%s', $dm->getField('reason'), $dm->getOperator(), $end_time);
		
		$userDm = new PwUserInfoDm($dm->getField('uid'));
		$userDm->setBanSign(true);
	//		->setBbsSign($newSign)
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$userDs->editUser($userDm, PwUser::FETCH_MAIN | PwUser::FETCH_INFO);
		$s = 1 << (PwUser::STATUS_BAN_SIGN - 1);
		return intval($info['status'] + $s);
	}
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::deleteBan()
	 */
	public function deleteBan($uid) {
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$info = $userDs->getUserByUid($uid, PwUser::FETCH_MAIN);
		if (!Pw::getstatus($info['status'], PwUser::STATUS_BAN_SIGN)) return $info['status'];//已经解禁不需要再次更改
		
		$userDm = new PwUserInfoDm($uid);
		$userDm->setBanSign(false);
	//		->setBbsSign('')
		/* @var $userDs PwUser */
		$userDs = Wekit::load('SRV:user.PwUser');
		$userDs->editUser($userDm, PwUser::FETCH_MAIN | PwUser::FETCH_INFO);
		$s = 1 << (PwUser::STATUS_BAN_SIGN - 1);
		return intval($info['status'] - $s);
	}
	
	/* (non-PHPdoc)
	 * @see PwUserBanTypeInterface::getExtension()
	 */
	public function getExtension($fid) {
		return '全局';
	}
}