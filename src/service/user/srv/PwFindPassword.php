<?php
Wind::import('LIB:utility.PwMail');
Wind::import('SRV:user.dm.PwUserInfoDm');
/**
 * 用户找回密码服务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFindPassword.php 24177 2013-01-22 10:36:09Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwFindPassword {
	/* @var $byMobileNum int 用手机找回密码可以使用的次数限制 */
	private $byMobileNum = 5;
	/* @var $byEmaialNum int 用邮箱找回密码可以使用的次数限制 */
	private $byEmailNum = 5;
	/* @var $spaceDay int 达到次数上限的时候相隔下次可以使用的时间间隔（单位：天）*/
	private $spaceDay = 1;
	private $info = array();
	
	const WAY_EMAIL = 'email';
	const WAY_MOBILE = 'mobile';
	
	/**
	 * 构造函数
	 *
	 * @param string $username
	 */
	public function __construct($username) {
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$info = $userDs->getUserByName($username, PwUser::FETCH_MAIN | PwUser::FETCH_DATA | PwUser::FETCH_INFO);
		if (!$info) {
			$info = $this->_getWindid()->getUser($username, 2);
			if (!$info) return;
			Wind::import('SRV:user.srv.PwRegisterService');
			$registerService = new PwRegisterService();
			$info = $registerService->sysUser($info['uid']);
			if ($info) {
				$info = array_merge($info, $this->_getUserDs()->getUserByUid($info['uid'], PwUser::FETCH_INFO));
			}
		}
		$this->info = $info;
	}
	
	/**
	 * 检查邮箱是否正确
	 *
	 * @param string $email 邮箱
	 * @return boolean|PwError
	 */
	public function checkEmail($email) {
		if (!$this->info) return new PwError('USER:illegal.request');
		if ($this->info['email'] != $email) {
			return new PwError('USER:findpwd.error.email');
		}
		if (true !== ($check = $this->allowFindBy(self::WAY_EMAIL))) return $check;
		return true;
	}
	
	/**
	 * 发送重置邮件
	 *
	 * @param string $state 加密串
	 * @return boolean
	 */
	public function sendResetEmail($state) {
		if (true !== ($check = $this->allowFindBy(self::WAY_EMAIL))) return $check;
		//TODO 产生激活码的方法
		$code = substr(md5(Pw::getTime()), mt_rand(1, 8), 8);
		$url = WindUrlHelper::createUrl('u/findPwd/resetpwd', array('code' => $code, '_statu' => $state));
		list($title, $content) = $this->_buildTitleAndContent($this->info['username'], $url);
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$activeCodeDs->addActiveCode($this->info['uid'], $this->info['email'], $code, Pw::getTime(), PwUserActiveCode::RESETPWD);
		$mail = new PwMail();
		$mail->sendMail($this->info['email'], $title, $content);
		return true;
	}

	/**
	 * 重置的邮箱验证码是否有效
	 *
	 * @param string $email 重置的email地址
	 * @param string $code  重置码
	 * @return PwError|boolean
	 */
	public function checkResetEmail($email, $code) {
		if (empty($this->info) || $this->info['email'] != $email) {
			return new PwError('USER:illegal.request');
		}
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$info = $activeCodeDs->getInfoByUid($this->info['uid'], PwUserActiveCode::RESETPWD);
		if (!$info || $info['email'] != $email || $info['code'] != $code) return new PwError("USER:findpwd.email.code.expired");
		/*找回密码：验证码不需要过期及验证机制*/
		if ($info['active_time'] > 0 ) return new PwError('USER:findpwd.email.code.expired');
		/*$validTime = $this->activeCodeValidTime * 3600;
		if (($info['send_time'] + $validTime) < Pw::getTime()) return new PwError('USER:active.email.overtime');*/
//		$activeCodeDs->activeCode($this->info['uid'], Pw::getTime());
		return true;
	}
	
	/**
	 * 获得信息的标题和内容
	 *
	 * @param string $titleKey   标题key
	 * @param string $contentKey 内容key
	 * @param string $username 用户名
	 * @param string $url 链接地址
	 * @return array
	 */
	private function _buildTitleAndContent($username, $url = '') {
		$search = array('{username}', '{sitename}');
		$replace = array($username, Wekit::C('site', 'info.name'));
		$title = str_replace($search, $replace, Wekit::C('login', 'resetpwd.mail.title'));
		$search[] = '{time}';
		$search[] = '{url}';
		$replace[] = Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s');
		$replace[] = $url ? sprintf('<a href="%s">%s</a>', $url, $url) : '';
		$content = str_replace($search, $replace, Wekit::C('login', 'resetpwd.mail.content'));
		return array($title, $content);
	}
	
	/**
	 * 获得邮箱的模糊显示
	 *
	 * @return string
	 */
	public function getFuzzyEmail() {
		$email = $this->info['email'];
		$info = explode('@', $email);
		$info[0] = $info[0][0] . str_repeat('*', strlen($info[0]) - 1);
		return implode('@', $info);
	}
	
	/**
	 * 获得email链接地址
	 *
	 * @return string
	 */
	public function getEmailUrl() {
		$email = $this->info['email'];
		$info = explode('@', $email);
		return 'http://mail.' . $info[1];
	}
	
	/**
	 * 是否可以使用邮箱找回密码
	 *
	 * @return boolean
	 */
	public function allowFindByMail() {
		if (false === $this->isBindMail()) return new PwError('USER:findpwd.notbind.email');
		return $this->allowFindBy(self::WAY_EMAIL);
	}
	
	/**
	 * 是否可以使用手机号码找回密码
	 *
	 * @return boolean
	 */
	public function allowFindByMobile() {
		if (false === $this->isBindMobile()) return new PwError('USER:findpwd.notbind.mobile');
		return $this->allowFindBy(self::WAY_MOBILE);
	}
	
	/**
	 * 是否绑定email
	 *
	 * @return boolean
	 */
	public function isBindMail() {
		return $this->info['email'] ? true : false;
	}
	
	
	/**
	 * 是否绑定手机号码
	 *
	 * @return boolean
	 */
	public function isBindMobile() {
		return $this->info['mobile'] ? true : false;
	}
	
	/**
	 * 判断当天通过邮箱找回密码是否已经超过次数限制
	 *
	 * @return boolean
	 */
	public function isOverByMail() {
		return $this->allowFindBy(self::WAY_EMAIL) instanceof PwError ? true : false;
	}
	
	/**
	 * 判断当天通过手机找回密码是否已经超过次数限制
	 *
	 * @return boolean
	 */
	public function isOverByMobile() {
		return $this->allowFindBy(self::WAY_MOBILE) instanceof PwError ? true : false;
	}
	
	/**
	 * 更改成功
	 *
	 * @param string $type
	 * @return boolean
	 */
	public function success($type) {
		//更新找回密码验证码
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$activeCodeDs->activeCode($this->info['uid'], Pw::getTime());
		return $this->setRecode($type);
	}
	
	/**
	 * 创建找回密码的唯一标识
	 *
	 * @param string $username 需要找回密码的用户名
	 * @param string $way 找回方式标识
	 * @param string $value 找回方式对应的值
	 * @return string
	 */
	public static function createFindPwdIdentify($username, $way, $value) {
		$code = Pw::encrypt($username . '|' . $way . '|' . $value, Wekit::C('site', 'hash') . '___findpwd');
		return rawurlencode($code);
	}
	
	/**
	 * 解析找回密码的标识
	 *
	 * @param string $identify
	 * @return array array($username, $way, $value)
	 */
	public static function parserFindPwdIdentify($identify) {
		return explode("|", Pw::decrypt(rawurldecode($identify), Wekit::C('site', 'hash') . '___findpwd'));
	}
	
	/**
	 * 根据方式获取对应的用户字段
	 *
	 * @param string $way
	 * @return string
	 */
	public static function getField($way) {
		return $way == self::WAY_MOBILE ? 'mobile' : 'email';
	}
	
	/**
	 * 获得尝试错误记录
	 *
	 * 在findpwd中保存格式为：0000-00-00:num|0000-00-00:num
	 * 0000-00-00:最后更新该记录的时间
	 * num : 最后更新记录的时候此种方式已经尝试的次数
	 * |: 在|左侧的，是用“邮箱”方式找回的记录，在|右侧，是用“手机”方式找回的记录
	 * @param string $type 找回类型
	 * @return boolean|PwError
	 */
	private function allowFindBy($type = self::WAY_EMAIL) {
		$findPwd = $this->info['findpwd'];
		if (!$findPwd) return true;
		$typeCode = $type == self::WAY_MOBILE ? 0 : 1;
		$recodes = explode('|', $findPwd);
		if (empty($recodes[$typeCode])) return true;
		$tryNum = $type == self::WAY_MOBILE ? $this->byMobileNum : $this->byEmailNum;
		list($time, $num) = explode(':', $recodes[$typeCode]);
		if (($time != Pw::time2str(Pw::getTime(), 'Y-m-d')) || $num < $tryNum) return true;
		return new PwError('USER:findpwd.over.limit.' . $type);
	}
	
	/**
	 * 设置重新找回记录
	 *
	 * 在findpwd中保存格式为：0000-00-00:num|0000-00-00:num
	 * 0000-00-00:最后更新该记录的时间
	 * num : 最后更新记录的时候此种方式已经尝试的次数
	 * |: 在|左侧的，是用“邮箱”方式找回的记录，在|右侧，是用“手机”方式找回的记录
	 * @param string $type
	 * @return true
	 */
	public function setRecode($type = self::WAY_EMAIL) {
		$findPwd = $this->info['findpwd'];
		$recodes = array(0 => '', 1 => '');
		$typeCode = $type == self::WAY_MOBILE ? 0 : 1;
		$tryTime = $type == self::WAY_MOBILE ? $this->byMobileNum : $this->byEmailNum;
		/*如果重来没有尝试找回过密码*/
		if (!$findPwd) {
			$recodes[$typeCode] = Pw::time2str(Pw::getTime(), 'Y-m-d') . ':1';
		} else {
			list($recodes[0], $recodes[1]) = explode('|', $findPwd);
			/*如果该方式的找回密码方式没有尝试过*/
			if (!($recode = $recodes[$typeCode])) {
				$recodes[$typeCode] = Pw::time2str(Pw::getTime(), 'Y-m-d') . ':1';
			/*如果该方式的找回密码方式尝试过*/
			} else {
				list($time, $num) = explode(':', $recode);
				/*如果该方式的上次找回密码不是在今天发生，那么这是今天第一次找回密码*/
				if (($time != Pw::time2str(Pw::getTime(), 'Y-m-d'))) {
					$recodes[$typeCode] = Pw::time2str(Pw::getTime(), 'Y-m-d') . ':1';
				/*如果今天不是第一次尝试找回密码，并且今天尝试找回密码的次数已经超过规定次数，抛出错误*/
				} elseif ($num >= $tryTime) {
					return new PwError('USER:findpwd.over.limit.' . $type);
				/*否则记录今天找回密码的次数*/
				} else {
					$recodes[$typeCode] = $time . ':' . ($num + 1);
				}
			}
		}
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$userdm = new PwUserInfoDm($this->info['uid']);
		$userdm->setFindpwd(implode('|', $recodes));
		return $userDs->editUser($userdm, PwUser::FETCH_DATA);
	}
	
	protected function _getWindid() {
		return WindidApi::api('user');
	}
}