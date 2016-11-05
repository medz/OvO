<?php
/**
 * 邀请链接相关操作
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwInviteFriendService.php 19073 2012-10-10 08:33:40Z xiaoxia.xuxx $
 * @package src.service.inviate.srv
 */
class PwInviteFriendService {
	private $_inviteCodeKey = 'abc@2012';

	/**
	 * 邀请注册一个用户
	 *
	 * @param string $inviteCode
	 * @param int $invited_uid
	 * @return boolean|PwError
	 */
	public function inviteRegist($inviteCode, $invited_uid) {
		$aUser = $this->checkInviteCode($inviteCode);
		if (!is_array($aUser)) return $aUser;
		
		$bUser = $this->_getUserDs()->getUserByUid($invited_uid);
		if (!$bUser) return new PwError('USER:invite.friend.code.invitedUid.error');
		
		/* @var $inviteLogDs PwInviteFriendLog */
		/* $inviteLogDs = Wekit::load('invite.PwInviteFriendLog');
		$inviteLogDs->addLog($aUser['uid'], $invited_uid, Pw::getTime()); */
		
		//邀请成功，被邀请者关注邀请者
		/* @var $attentionSrv PwAttentionService */
		$attentionSrv = Wekit::load('attention.srv.PwAttentionService');
		$attentionSrv->addFollow($invited_uid, $aUser['uid']);
// 		$attentionSrv->addFollow($aUser['uid'], $invited_uid);
		
		//邀请成功，增加积分
		/* $inviteConfig = Wekit::getConfig('site');
		$credit = array($inviteConfig['invite.friend.credit.type'] => $inviteConfig['invite.friend.credit.num']); */
		/* @var $creditBo PwCreditBo */
		/* $creditBo = PwCreditBo::getInstance();
		$creditBo->addLog('invite_friend', $credit, new PwUserBo($aUser['uid']), array('friendName' => $bUser['username']));
		$creditBo->writeLog(); */
		
		// 邀请成功--邀请好友的任务
// 		PwSimpleHook::getInstance('PwInviteFriendService_invite')->runDo($aUser['uid'], $invited_uid);
		return true;
	}
	
	/**
	 * 邀请
	 *
	 * @param string $inviteCode 邀请链接的邀请码
	 * @param int $invited_uid   接受邀请的用户ID
	 * @return boolean
	 */
	public function invite($inviteCode, $invited_uid) {
		if (true !== ($result = $this->allowInvite())) return $result;
		$aUser = $this->checkInviteCode($inviteCode);
		if (!is_array($aUser)) return $aUser;
		
		$bUser = $this->_getUserDs()->getUserByUid($invited_uid);
		if (!$bUser) return new PwError('USER:invite.friend.code.invitedUid.error');
		if ($aUser['uid'] == $bUser['uid']) return new PwError('USER:invite.friend.invite.self');
		
		$r = $this->_addFans($aUser, $bUser);
		if (true !== $r) return $r;
		return $aUser;
		
	}
	
	
	/**
	 * 检测是否可以使用链接邀请
	 * 
	 * 如果开启了邀请注册，则抛出错误（如果已经开启了邀请注册，则好友邀请不可用）
	 * 否则，允许使用
	 *
	 * @return PwError|boolean
	 */
	public function allowInvite() {
// 		$invite_config = Wekit::C('site');
// 		if (!$invite_config['invite.friend.isOpen']) return new PwError('USER:invite.friend.code.close');
		if (2 == Wekit::C('register', 'type')) return new PwError('USER:invite.friend.code.dumplic');
		return true;
	}
	
	/**
	 * 检查邀请链接是否有效
	 *
	 * @param string $code
	 * @return PwError|array
	 */
	public function checkInviteCode($code) {
		$uid = $this->parseInviteCode($code);
		$info = $this->_getUserDs()->getUserByUid($uid);
		if (!$info) return new PwError('USER:invite.friend.code.illage');
		return $info;
	}
	
	/**
	 * 解析邀请链接码
	 *
	 * @param string $code
	 * @return int
	 */
	public function parseInviteCode($code) {
		$uid = intval(substr($code, 0, strlen($code) - 16));
		if ($uid < 1) return 0;
		if ($code != ($uid . substr(md5($uid . $this->_inviteCodeKey), -16))) return 0;
		return $uid;
	}
	
	/**
	 * 根据输入的用户ID构建该用户的邀请链接码
	 *
	 * @param int $uid
	 * @return string
	 */
	public function createInviteCode($uid) {
		return $uid . substr(md5($uid . $this->_inviteCodeKey), -16);
	}

	/**
	 * 互相加为好友
	 *
	 * @param array $aUser
	 * @param array $bUser
	 * @return PwError|boolean
	 */
	private function _addFans($aUser, $bUser) {
		/* @var $attentionDs PwAttention */
		$attentionDs = Wekit::load('attention.PwAttention');
		$aFollowedB = $attentionDs->isFollowed($aUser['uid'], $bUser['uid']);
		$bFollowedA = $attentionDs->isFollowed($bUser['uid'], $aUser['uid']);
	
		if ($aFollowedB && $bFollowedA) {
			return new PwError('USER:invite.friend.exists', array('{name}' => $aUser['username']));
		} elseif (!$aFollowedB && !$bFollowedA) {
			//邀请成功，相互关注
			/* @var $attention PwAttentionService */
			$attention = Wekit::load('attention.srv.PwAttentionService');
			$attention->addFollow($aUser['uid'], $bUser['uid']);
			$attention->addFollow($bUser['uid'], $aUser['uid']);
		} elseif ($aFollowedB) {
			/* @var $attention PwAttentionService */
			$attention = Wekit::load('attention.srv.PwAttentionService');
			$attention->addFollow($bUser['uid'], $aUser['uid']);
		} elseif ($bFollowedA) {
			/* @var $attention PwAttentionService */
			$attention = Wekit::load('attention.srv.PwAttentionService');
			$attention->addFollow($aUser['uid'], $bUser['uid']);
		}
		return true;
	}
	
	/**
	 * 用户DS
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
}