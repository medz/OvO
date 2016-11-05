<?php
Wind::import('SRV:user.PwUser');
Wind::import('SRV:user.dm.PwUserInfoDm');
Wind::import('SRV:credit.bo.PwCreditBo');
Wind::import('WIND:utility.WindValidator');
Wind::import('SRV:user.validator.PwUserValidator');

/**
 * 用户登录服务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLoginService.php 24931 2013-02-27 01:16:51Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwLoginService extends PwBaseHookService {
	
	private $loginConfig = array();
	private $ipLimit = 100;
	
	/**
	 * 尝试次数达到最高次数之后，一段时间30分钟内不能再登录
	 * 
	 * @var int $trySpace 
	 */
	private $trySpace = 1800;
	
	public function __construct() {
		parent::__construct();
		$this->loginConfig = Wekit::C('login');
	}
	
	/**
	 * 用户登录
	 *
	 * @param string $username 用户登录的帐号
	 * @param string $password 用户登录的密码
	 * @param string $ip 登录IP
	 * @param string $safeQuestion 安全问题
	 * @param string $safeAnswer 安全问题答案
	 * @return boolean|int
	 */
	public function login($username, $password, $ip, $safeQuestion = null, $safeAnswer = '') {
		$checkQ = !is_null($safeQuestion) ? true : false;
		Wind::import("SRV:user.srv.PwTryPwdBp");
		$pwdBp = new PwTryPwdBp();
		$info = $pwdBp->auth($username, $password, $ip, $checkQ, $safeQuestion, $safeAnswer);
		if ($info instanceof PwError) return $info;
		if (($result = $this->runWithVerified('afterLogin', $info)) instanceof PwError) return $result;
		return $info;
	}
	
	/**
	 * 登录检查输入
	 *
	 * @param string $username
	 * @return array
	 */
	public function checkInput($username) {
		$r = array();
		//手机号码登录
		if (PwUserValidator::isMobileValid($username) === true && in_array(4, $this->loginConfig['ways'])) {
			$mobileInfo = Wekit::load('user.PwUserMobile')->getByMobile($username);
			if (!$mobileInfo) return array();
			$r = $this->_getWindid()->getUser($mobileInfo['uid'], 1);
		}
		//UID登录
		if (!$r && is_numeric($username) && in_array(1, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->getUser($username, 1);
		}
		
		//email登录
		if (!$r && WindValidator::isEmail($username) && in_array(2, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->getUser($username, 3);
		}
		//用户名登录
		if (!$r && in_array(3, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->getUser($username, 2);
		}
		return $r;
	}
	
	/**
	 * 设置登录cookie
	 *
	 * @param PwUserBo $userBo
	 * @param string $ip
	 * @param int $rememberme
	 */
	public function setLoginCookie(PwUserBo $userBo, $ip, $rememberme = 0) {
		//登录成功，将用户该次登录的尝试密码记录清空
		Wind::import("SRV:user.srv.PwTryPwdBp");
		$pwdBp = new PwTryPwdBp();
		$pwdBp->restoreTryRecord($userBo->uid, '');

		/* @var $userService PwUserService */
		$userService = Wekit::load('user.srv.PwUserService');
		$userService->createIdentity($userBo->uid, $userBo->info['password'], $rememberme);
		return $this->welcome($userBo, $ip);
	}
	
	/**
	 * 登录扩展点 m_PwLoginService
	 * 
	 * @see PwUserLoginDoBase::welcome()
	 */
	public function welcome(PwUserBo $userBo, $ip) {
		/* @var $userService PwUserService */
		$userService = Wekit::load('user.srv.PwUserService');
		$userService->updateLastLoginData($userBo->uid, $ip);
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->operate('login', $userBo);
		
		return $this->runDo('welcome', $userBo, $ip);
	}
	
	/**
	 * 创建登录标识
	 *
	 * @param array $userInfo 用户信息
	 * @return string
	 */
	public static function createLoginIdentify($userInfo) {
		$code = Pw::encrypt($userInfo['uid'] . "\t" . Pw::getPwdCode($userInfo['password']) . "\t" . Pw::getTime());
		return rawurlencode($code);
	}
	
	/**
	 * 解析登录标识
	 *
	 * @param string $identify 需要解析的标识
	 * @return array array($uid, $password)
	 */
	public static function parseLoginIdentify($identify) {
		$args = explode("\t", Pw::decrypt(rawurldecode($identify)));
		if ((Pw::getTime() - $args[2]) > 300) {
			return array(0, '');
		} else {
			return $args;
		}
	}
		
	/** 
	 * 获得用户Ds
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
	
	/** 
	 * 获得windidDS
	 *
	 * @return WindidUserApi
	 */
	protected function _getWindid() {
		return WindidApi::api('user');
	}
		
	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return 'PwUserLoginDoBase';
	}
}
