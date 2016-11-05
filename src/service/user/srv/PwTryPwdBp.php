<?php
Wind::import('SRV:user.validator.PwUserValidator');
/**
 * 用户各种尝试密码相关业务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTryPwdBp.php 24520 2013-01-31 07:11:48Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwTryPwdBp {
	
	private $loginConfig = array();
	/**
	 * 每日同一个IP地址允许尝试错误密码的总次数
	 * @var int $errIplimit
	 */
	private $errIplimit = 100;
	/**
	 * 尝试次数达到最高次数之后，一段时间30分钟内不能再登录
	 * 
	 * @var int $nextTrySpace 单位秒
	 */
	private $nextTrySpace = 1800;

	/**
	 * 修改相关尝试密码次数限制
	 * @var int $configTotal
	 */
	private $configTotal = 5;


    private $ip = "";

	/**
	 * 构造信息
	 */
	public function __construct($config = array()) {
		$_siteConfig = Wekit::C('login');
		$this->initConfig($config ? array_merge($_siteConfig, $config) : $_siteConfig);
	}


	/** 
	 * 获得登录用户信息
	 *
	 * @param string $username 登录输入
	 * @param string $password 密码
	 * @param string $ip 尝试的IP地址
	 * @param boolean $checkQ 是否验证安全问题
	 * @param string $safeQuestion 安全问题
	 * @param string $safeAnswer 安全问题答案
	 * @return array
	 */
	public function auth($username, $password, $ip = '', $checkQ = false, $safeQuestion = '', $safeAnswer = '') {
		$r = array(-14, array());
		//手机号码登录
		if (PwUserValidator::isMobileValid($username) === true && in_array(4, $this->loginConfig['ways'])) {
			$mobileInfo = Wekit::load('user.PwUserMobile')->getByMobile($username);
			if (!$mobileInfo) return $this->checkVerifyResult(-1, array());
			$r = $this->_getWindid()->login($mobileInfo['uid'], $password, 1, $checkQ, $safeQuestion, $safeAnswer);
		}
		//UID登录
		if ($r[0] == -14 && is_numeric($username) && in_array(1, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->login($username, $password, 1, $checkQ, $safeQuestion, $safeAnswer);
		}
		
		//email登录
		if ($r[0] == -14 && WindValidator::isEmail($username) && in_array(2, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->login($username, $password, 3, $checkQ, $safeQuestion, $safeAnswer);
		}
		//用户名登录
		if ($r[0] == -14 && in_array(3, $this->loginConfig['ways'])) {
			$r = $this->_getWindid()->login($username, $password, 2, $checkQ, $safeQuestion, $safeAnswer);
        }
        //
        $this->ip = $ip;
        //
		return $this->checkVerifyResult($r[0], $r[1]);
	}

	/**
	 * 检查用户密码是否正确
	 * @param string $uid 用户uid
	 * @param string $password 密码
	 * @param string $ip 尝试的IP地址
	 * @param boolean $checkQ 是否验证安全问题
	 * @param string $safeQuestion 安全问题
	 * @param string $safeAnswer 安全问题答案
	 */
	public function checkPassword($uid, $password, $ip = '', $checkQ = false, $safeQuestion = '', $safeAnswer = '') {
		$r = $this->_getWindid()->login($uid, $password, 1, $checkQ, $safeQuestion, $safeAnswer);
		return $this->checkVerifyResult($r[0], $r[1]);
	}

	/** 
	 * 检查安全问题和密码
	 *
	 * @param int $uid 用户信息
	 * @param string $question 安全问题
	 * @param string $answer 安全问题答案
	 * @return PwError
	 */
	public function checkQuestion($uid, $question, $answer, $ip) {
		$info = $this->_getWindid()->getUser($uid, 1);
		if (!$info) {
			return new PwError('USER:user.error.-14');
		}
		if (true !== ($r = $this->allowTryAgain($uid, $ip, 'question'))) {
			return $r;
		}
		if ($this->_getWindid()->checkQuestion($uid, $question, $answer) > 0) {
			return true;
		}
		return $this->updateTryRecord($info['uid'], $ip, 'question');
	}

	/** 
	 * 检查用户是否已经超过尝试设置的次数
	 *
	 * @param int $uid
	 * @return boolean|PwError
	 */
	public function allowTryAgain($uid, $ip, $type = 'pwd') {
		//Ip限制添加
		if (true !== ($_result = $this->checkIpLimit($ip,true))) {
			return $_result;
		}
		//密码次数测试
		$info = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_DATA);
		if (!$info || !$info['trypwd']) {
			$num = $lastTry = 0;
		} else {
			list($lastTry, $num) = explode('|', $info['trypwd']);
		}
		//尝试次数达到上限同时帐号还在被冻结状态
		if ($num >= $this->configTotal && (Pw::getTime() - $lastTry) <= $this->nextTrySpace) {
			return new PwError('USER:login.error.tryover.' . $type, array('{totalTry}' => $this->configTotal, '{min}' => $this->nextTrySpace / 60));
		}
		return true;
	}

	/** 
	 * 跟新用户的尝试信息
	 *
	 * @param int $uid 用户ID
	 * @param string $ip 登录的IP地址
	 * @param string $type 记录类型
	 * @return PwError
	 */
	public function updateTryRecord($uid, $ip, $type = 'pwd') {
		if (true !== ($isIpOver = $this->checkIpLimit($ip, true))) return $isIpOver;
		$info = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_DATA);
		if (!$info || !$info['trypwd']) {
			$num = $lastTry = 0;
		} else {
			list($lastTry, $num) = explode('|', $info['trypwd']);
		}
		$now = Pw::getTime();
		//尝试的次数没有达到上限
		if ($num < $this->configTotal) {
			$num = ($lastTry == 0 || ($now - $lastTry) >= $this->nextTrySpace) ? 1 : $num + 1;
			$this->restoreTryRecord($info['uid'], $now . '|' . $num);
			if ($num == $this->configTotal) {
				return new PwError('USER:login.error.tryover.' . $type, array('{totalTry}' => $this->configTotal, '{min}' => $this->nextTrySpace / 60));
			} else {
				return new PwError('USER:login.error.' . $type, array('{num}' => $this->configTotal - $num));
			}
		//尝试的次数已经达到上限，同时上次错误的时间距离现在已经大于30分钟
		} elseif (($now - $lastTry) > $this->nextTrySpace) {
			$this->restoreTryRecord($info['uid'], $now . '|1');
			return new PwError('USER:login.error.' . $type, array('{num}' => $this->configTotal - 1));
		}
		//如果尝试的次数已经达到上限，并且上次错误的时间距离现在没有超过30分钟
		return new PwError('USER:login.error.tryover.' . $type, array('{totalTry}' => $this->configTotal, '{min}' => $this->nextTrySpace / 60));
	}
	
	/**
	 * 更新尝试次数的记录
	 *
	 * @param int $uid
	 * @param string $tryPwd
	 * @return boolean|PwError
	 */
	public function restoreTryRecord($uid, $tryPwd) {
		$userdm = new PwUserInfoDm($uid);
		$userdm->setTrypwd($tryPwd);
		return $this->_getUserDs()->editUser($userdm, PwUser::FETCH_DATA);
	}

	/**
	 * 检查验证结果
	 * @param int $status
	 * @param array $info
	 * @return array|PwError
	 */
	protected function checkVerifyResult($status, $info) {
		switch ($status) {
			case 1://用户信息正常
				if (true !== ($r = $this->allowTryAgain($info['uid'], $this->ip))) {
					return $r;
				}
				break;
				//return array(1, $r[1]);
			case -13://用户密码错误
				Wind::import('SRV:log.PwLogLogin');
				Wind::import('SRV:log.dm.PwLogLoginDm');
				$dm = new PwLogLoginDm($info['uid']);
				$dm->setUsername($info['username'])
					->setTypeid(PwLogLogin::ERROR_PWD)
					->setIp($ip)
					->setCreatedTime(Pw::getTime());
				Wekit::load('SRV:log.PwLogLogin')->addLog($dm);
				return $this->updateTryRecord($info['uid'], $this->ip, 'pwd');
				//return array(-2, $r[1]);
			case -20://用户安全问题错误
				Wind::import('SRV:log.PwLogLogin');
				Wind::import('SRV:log.dm.PwLogLoginDm');
				$dm = new PwLogLoginDm($info['uid']);
				$dm->setUsername($info['username'])
					->setIp($ip)
					->setCreatedTime(Pw::getTime())
					->setTypeid(PwLogLogin::ERROR_SAFEQ);
				Wekit::load('SRV:log.PwLogLogin')->addLog($dm);
				return $this->updateTryRecord($info['uid'], $this->ip, 'question');
				//return array(-3, $r[1]);
			case -14://用户不存在
			default:
				return new PwError('USER:user.error.-14');
				//return array(-1, array());
		}
		return $info;
	}

	/**
	 * 检查IP的限制
	 *
	 * @param string $ip
	 * @param boolean $isUpdate
	 * @return boolean|PwError
	 */
	private function checkIpLimit($ip, $isUpdate = false) {
		if (!$ip) return true;
		/* @var $ipDs PwUserLoginIpRecode */
		$ipDs = Wekit::load('user.PwUserLoginIpRecode');
		$info = $ipDs->getRecode($ip);
		$tody = Pw::time2str(Pw::getTime(), 'Y-m-d');
		if (!$info) {
			$info['error_count'] = 0;
			$info['last_time'] = $tody;
		}
		//不是今天的则先清空
		($info['last_time'] != $tody) && $info['error_count'] = 0;
		if ($info['error_count'] >= $this->errIplimit) {
			return new PwError('USER:login.error.ip.tryover', array('{num}' => $this->errIplimit));
		}
		if (true === $isUpdate) {
			$error_count = $info['error_count'] + 1;
			$ipDs->updateRecode($ip, $tody, $error_count);
		}
		return true;
	}
	
	/**
	 * 初始化配置信息
	 */
	private function initConfig($config) {
		$this->loginConfig = $config;
		if (isset($config['security.errIplimit']) && ($_limit = intval($config['security.errIplimit'])) > 0) {
			$this->errIplimit = $_limit;
		}
		if (isset($config['security.nextTrySpace']) && ($_limit = intval($config['security.nextTrySpace'])) > 0) {
			$this->nextTrySpace = $_limit;
		}
		if (isset($config['trypwd']) && ($_limit = intval($config['trypwd'])) > 0) {
			$this->configTotal = $_limit;
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
}
