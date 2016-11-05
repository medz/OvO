<?php
/**
 * 用户注册过滤
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: UserRegisterFilter.php 18671 2012-09-26 02:49:27Z xiaoxia.xuxx $
 * @package 
 */
class UserRegisterFilter extends WindActionFilter {

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::preHandle()
	 */
	public function preHandle() {
		/* @var $userBo PwUserBo */
		$userBo = Wekit::getLoginUser();
		if ($userBo->isExists() && !in_array($this->router->getAction(), array('welcome', 'guide'))) {
			
			//TODO 好友邀请链接
			$inviteCode = $this->getInput('invite');
			if ($inviteCode) {
				$user = Wekit::load('SRV:invite.srv.PwInviteFriendService')->invite($inviteCode, $userBo->uid);
				if ($user instanceof PwError) {
					$this->showError($user->getError());
				}
			}
			if (strtolower($this->router->getAction()) == strtolower('activeEmail')) {
				$referer = Wekit::C('site', 'info.url');
			} else {
				$referer = $this->getRequest()->getServer('HTTP_REFERER');
			}
			$this->errorMessage->addError($referer ? $referer : WindUrlHelper::createUrl(''), 'referer');
			$this->errorMessage->addError(2, 'refresh');
			$this->errorMessage->sendError('USER:register.dumplicate');
		}
	}

	/* (non-PHPdoc)
	 * @see WindHandlerInterceptor::postHandle()
	 */
	public function postHandle() {
	}
}