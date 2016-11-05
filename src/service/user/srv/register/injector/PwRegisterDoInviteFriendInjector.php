<?php

/**
 * 用户注册-链接邀请
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwRegisterDoInviteInjector.php 6177 2012-03-19 03:50:39Z xiaoxia.xuxx $
 * @package src.service.user.srv.register.ingector
 */
class PwRegisterDoInviteFriendInjector extends PwBaseHookInjector {
	
	/** 
	 * 链接邀请的注入插件
	 *
	 * @return PwRegisterDoInviteFriend
	 */
	public function run() {
		$registerInvite = null;
		if (!$this->bp->isOpenInvite && $this->getInput('invite')) {
			Wind::import('SRV:user.srv.register.do.PwRegisterDoInviteFriend');
			$registerInvite = new PwRegisterDoInviteFriend($this->bp, $this->getInput('invite'));
		}
		return $registerInvite;
	}
}