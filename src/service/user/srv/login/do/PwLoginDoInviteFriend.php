<?php
Wind::import('SRV:user.srv.login.PwUserLoginDoBase');

/**
 * 执行登录之后用户
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package src.service.user.srv.login
 */
class PwLoginDoInviteFriend extends PwUserLoginDoBase {
	private $code = '';
	private $srv = null;
	
	/**
	 * 构造函数
	 *
	 * @param PwLoginService $pwUserLogin
	 * @param string $code
	 */
	public function __construct(PwLoginService $pwUserLogin, $code) {
		$this->srv = $pwUserLogin;
		$this->code = $code;
	}
	
	/* (non-PHPdoc)
	 * @see PwUserLoginDoBase::afterLogin()
	 */
	public function afterLogin($info) {
		if (2 == Wekit::C('register', 'type')) return true;
		
		if ($this->code) {
			/* @var $inviteSrv PwInviteFriendService */
			$inviteSrv = Wekit::load('SRV:invite.srv.PwInviteFriendService');
			return $inviteSrv->invite($this->code, $info['uid']);
		}
		return true;
	}
}