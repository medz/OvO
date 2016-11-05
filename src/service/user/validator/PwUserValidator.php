<?php
/**
 * 用户的相关验证方法
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserValidator.php 24943 2013-02-27 03:52:21Z jieyin $
 * @package  src.service.user.validator
 */
class PwUserValidator {

	/**
	 * 检测用户名的合法性
	 *
	 * @param string $username
	 * @return boolean|PwError
	 */
	public static function isUsernameHasIllegalChar($username) {
		//匹配用户名只能含有中文、数字、大小写字母、'.'、_
		if (0 >= preg_match('/^[\x7f-\xff\dA-Za-z\.\_]+$/', $username)) {
			return new PwError('USER:error.username');
		}
		return false;
	}
	
	/** 
	 * 检查用户的手机号码是否合法
	 *
	 * @param string $password 用户密码
	 * @return PwError|boolean
	 */
	public static function isMobileValid($mobile) {
    	if (0 >= preg_match('/^1\d{10}$/', $mobile)) return new PwError('USER:mobile.error.formate');
		return true;
	}
	
	/**
	 * 检测固定电话号码是否正确
	 *
	 * @param string $telPhone
	 * @return true|PwError
	 */
	public static function isTelPhone($telPhone) {
		if (0 >= preg_match('/^[0-9][-\d]*\d*$/', $telPhone)) {
			return new PwError('USER:error.telphone');
		}
		return true;
	}
	
	/**
	 * 验证支付宝帐号
	 *
	 * @param string $alipay 待检查的支付宝帐号
	 * @param string $username 排除的用户名
	 * @return true|PwError
	 */
	public static function isAlipayValid($alipay, $username = '') {
		/* @var $userDs PwUser */
//		$userDs = Wekit::load('user.PwUser');
		//TODO【用户数据验证】支付宝帐号唯一验证
		return true;
	}
	
	
	/** 
	 * 检查用户的邮箱
	 *
	 * @param string $email 待检查的用户邮箱
	 * @param string $username  待检查的用户名
	 * @return boolean|PwError
	 */
	public static function isEmailValid($email, $username = '', $uid = 0) {
		$result = self::_getWindid()->checkUserInput($email, 3, $username, $uid);
		if ($result < 1) {
			return new PwError('USER:user.error.' . $result);
		}
		return true;
	}

	/** 
	 * 验证用户名
	 *
	 * @param string $username  验证的用户名
	 * @param int $uid			排除的用户ID
	 * @return PwError|boolean
	 */
	public static function isUsernameValid($username, $uid = 0) {
		if (!$username) return new PwError('USER:user.error.-1');
		$result = self::_getWindid()->checkUserInput($username, 1, '', $uid);
		if ($result < 1) {
			if ($result == -2) {
				$config = WindidApi::C('reg');
				return new PwError('WINDID:code.-2', array('{min}' => $config['security.username.min'], '{max}' => $config['security.username.max']));
			}
			return new PwError('WINDID:code.' . $result);
		}
		if (false !== ($r = self::isUsernameHasIllegalChar($username))) {
			return $r;
		}
		return true;
	}
	
	/** 
	 * 检查用户的username是否存在 
	 *
	 * @param string $username  待检查的用户名
	 * @param int $exceptUid 排除的用户ID
	 * @return boolean
	 */
	public static function checkUsernameExist($username, $exceptUid = 0) {
		$result = self::_getWindid()->checkUserInput($username, 1, '', $exceptUid);
		if ($result < 1) return new PwError('WINDID:code.'. $result);
		/* @var $userDs PwUser */
	/*	$userDs = Wekit::load('user.PwUser');
		$info = $userDs->getUserByName($username, PwUser::FETCH_MAIN);
		if (!$info) return false;
		$exceptUid = intval($exceptUid);
		if ($exceptUid && $info['uid'] == $exceptUid) return false;*/
		return true;
	}

	/** 
	 * 检查用户的密码是否合法
	 *
	 * @param string $password 用户密码
	 * @param string $username 用户名
	 * @return PwError|boolean
	 */
	public static function isPwdValid($password, $username) {
		$result = self::_getWindid()->checkUserInput($password, 2, $username);
		if ($result < 1) {
			$config = WindidApi::C('reg');
			$var = array('{min}' => $config['security.password.min'], '{max}' => $config['security.password.max']);
			return new PwError('WINDID:code.'. $result, $var);
		}
		$result = self::checkPwdComplex($password, $username);
		if ($result instanceof PwError) return $result;
		return true;
	}
	
	/**
	 * 验证密码的复杂度是否符合后台设置要求
	 * 检查密码复杂度
	 * 检查用户名和密码是否允许相同
	 * 如果设置不允许相同而相同则返回PwError
	 * 其余返回true
	 * 
	 * @param string $password 用户密码
	 * @param string $username 用户名
	 * @return boolean|PwError
	 */
	public static function checkPwdComplex($password, $username) {
		$register = WindidApi::C('reg');
		if (!($pwdConfig = $register['security.password'])) return true;
		$config = array_sum($pwdConfig);
		if (in_array(9, $pwdConfig)) {
			$config = $config - 9;
			if ($username == $password) return new PwError('USER:pwd.error.equalUsername');
		}
		if ($config == 0) return true;
		if (self::_complexCaculate($password, $config)) return new PwError('USER:pwd.error.complex', array('{type}' => self::buildPwdComplexMsg($pwdConfig)));
		return true;
	}
	
	/**
	 * 显示用户密码的支持信息
	 * 
	 * @return array(string, args)
	 */
	public static function buildPwdShowMsg() {
		$config = WindidApi::C('reg');
		$_min = $config['security.password.min']; 
		$_max = $config['security.password.max'];
		$_complex = $config['security.password'];
		$_length = $_min || $_max;
		$type = self::buildPwdComplexMsg($_complex);
		$var = array();
		$_key = 'USER:pwd.require';
		if ($_length && $_complex) {
			$_key = 'USER:pwd.format.require';
			$var = array('{type}' => $type, '{min}' => $_min, '{max}' => $_max);
		} elseif (!$_complex && $_length) {
			$_key = 'USER:pwd.format.length.require';
			$var = array('{min}' => $_min, '{max}' => $_max);
		} elseif (!$_length && $_complex) {
			$_key = 'USER:pwd.error.complex';
			$var = array('{type}' => $type);
		}
		return array($_key, $var);
	}
	
	/**
	 * 显示用户名的验证支持信息
	 * 
	 * @return array(string, args)
	 */
	public static function buildNameShowMsg() {
		$config = WindidApi::C('reg');
		$_name = 'USER:user.error.username';
		$_min = $config['security.username.min'];
		$_max = $config['security.username.max'];
		return array('USER:user.error.username', array('{min}' => $_min, '{max}' => $_max));
	}
	
	/**
	 * 构造用户密码复杂度的校验规则
	 *
	 * @param array $config 复杂规则的配置
	 * @return string
	 */
	private static function buildPwdComplexMsg($config) {
		if (!$config) return '';
		$complex = array(1 => '小写字母', 2 => '大写字母', 4 => '数字', 8 => '非空白符号', 9 => '不能和用户名相同');
		return implode('、', array_intersect_key($complex, array_flip($config)));
	}
	
	/**
	 * 复杂度判断
	 * 
	 * @param string $password 密码
	 * @param int $config 配置
	 * @return boolean
	 */
	private static function _complexCaculate($password, $config) {
		$pwdLen = strlen($password);
		$complex = 0;
		for ($i = 0; $i < $pwdLen; $i ++) {
			$ascii = ord($password[$i]);
			//必须含有小写字母 97-122 
			if (1 == ($config & 1) && $ascii >= 97 && $ascii <= 122) {
				if (0 == $complex || 1 != ($complex & 1)) $complex += 1;
				continue;
			}
			//必须含有大写字母 65-90
			if (2 == ($config & 2) && $ascii >= 65 && $ascii <= 90) {
				if (0 == $complex || 2 != ($complex & 2)) $complex += 2;
				continue;
			}
			//必须含有数字 48-57
			if (4 == ($config & 4) && $ascii >= 48 && $ascii <= 57) {
				if (0 == $complex || 4 != ($complex & 4)) $complex += 4;
				continue;
			}
			//必须含有符号 33-47/58-64/91-96/123-126
			if (8 == ($config & 8) && 
				(($ascii >= 33 && $ascii <=47) || ($ascii >= 58 && $ascii <= 64) || ($ascii >= 91 && $ascii <= 96) || ($ascii >= 123 && $ascii <= 126))) {
					if (0 == $complex || 8 != ($complex & 8)) $complex += 8;
					continue;
			}
			//已经达到设置复杂度则跳出
			if ($config == $complex) break;
		}
		return $config != $complex;
	}
	
	private static function _getWindid() {
		return WindidApi::api('user');
	}
}
?>