<?php
Wind::import('SRV:user.srv.register.do.PwRegisterDoBase');
Wind::import('SRV:credit.bo.PwCreditBo');
Wind::import('SRV:invite.dm.PwInviteCodeDm');
/**
 * 用户注册-邀请方式注册的服务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwRegisterDoInvite.php 24134 2013-01-22 06:19:24Z xiaoxia.xuxx $
 * @package src.service.user.srv.register.do
 */
class PwRegisterDoInvite extends PwRegisterDoBase {
	private $code = '';
	private $inviteInfo = array();
	
	/**
	 * 构造函数
	 *
	 * @param PwRegisterService $pwUserRegister
	 * @param string $code
	 */
	public function __construct(PwRegisterService $pwUserRegister, $code) {
		parent::__construct($pwUserRegister);
		$this->code = $code;
	}
	
	/* (non-PHPdoc)
	 * @param PwUserInfoDm $userDm
	 * @see PwRegisterDoBase::beforeRegister()
	 */
	public function beforeRegister(PwUserInfoDm $userDm) {
		if ($this->bp->config['type'] != 2) return false;
		/* @var $inviteService PwInviteCodeService */
		$inviteService = Wekit::load('invite.srv.PwInviteCodeService');
		if (($r = $inviteService->allowUseInviteCode($this->code)) instanceof PwError) {
			return $r;
		}
		$this->inviteInfo = $r;
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwRegisterDoBase::afterRegister()
	 */
	public function afterRegister(PwUserInfoDm $userDm) {
		if ($this->bp->config['type'] != 2) return false;
		/* @var $inviteDs PwInviteCode */
		$inviteDs = Wekit::load('invite.PwInviteCode');
		if ($this->inviteInfo['created_userid']) {
			$codeDm = new PwInviteCodeDm();
			$codeDm->setInvitedUid($userDm->uid)
				->setModifiedTime(Pw::getTime())
				->setIfused(1)
				->setCode($this->code);
			//别人赠送的邀请码
			$inviteDs->updateCode($codeDm);
			
			$creditType = $this->bp->config['invite.reward.credit.type'];
			$creditNum = $this->bp->config['invite.reward.credit.num'];
			//邀请人获得加奖励
			//[积分日志] 成功邀请好友积分奖励
			/* @var $creditBo PwCreditBo */
			$creditBo = PwCreditBo::getInstance();
			$creditBo->addLog('invite_reward', array($creditType => $creditNum), new PwUserBo($this->inviteInfo['created_userid']), array('friend' => $userDm->getField('username')));
			$creditBo->set($this->inviteInfo['created_userid'], $creditType, $creditNum);
			
			//邀请成功相互关注 被邀请者关注邀请者
			/* @var $attention PwAttentionService */
			$attention = Wekit::load('attention.srv.PwAttentionService');
			$attention->addFollow($userDm->uid, $this->inviteInfo['created_userid']);
//			$attention->addFollow($this->inviteInfo['created_userid'], $userDm->uid);
		} else {
			$codeDm = new PwInviteCodeDm();
			$codeDm->setInvitedUid($userDm->uid)
				->setIfused(1)
				->setModifiedTime(Pw::getTime())
				->setCreateUid($userDm->uid)
				->setCode($this->code);
			//自己购买的邀请码
			$inviteDs->updateCode($codeDm);
		}
		return true;
	}
}