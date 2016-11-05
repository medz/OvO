<?php

/**
 * 手机短信服务
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwMobileService {
	protected $plat;	
	protected $sendNumDay = 3;  //每天发送手机验证码次数
	public $platUrl;
	
	public function __construct() {
		$this->setPlat();
		$this->platUrl = $this->plat->platUrl;
	}
	
	/**
	 * 获取剩余短信数量
	 *
	 * @return int
	 */
	public function getRestMobileMessage() {
		if (!$this->plat) {
			return new PwError('USER:mobile.plat.choose.error');
		}
		return $this->plat->getRestMobileMessage();
	}

	/**
	 * 发送短信
	 *
	 * @return bool
	 */
	public function sendMobileMessage($mobile) {
		if (!$this->plat) {
			return new PwError('USER:mobile.plat.choose.error');
		}
		$code = $this->_buildCode();
		$content = $this->_buildContent($code);
		$number = $this->checkTodayNum($mobile);
		if ($number instanceof PwError) {
			return $number;
		}
		$result = $this->plat->sendMobileMessage($mobile, $content);
		if ($result instanceof PwError) return $result;
		Wind::import('SRV:user.dm.PwUserMobileDm');
		$dm = new PwUserMobileDm();
		$dm->setMobile($mobile)
			->setCode($code)
			->setNumber($number);
		$result = $this->_getDs()->addMobileVerify($dm);
		if ($result instanceof PwError) return $result;
		return true;
	}

	/**
	 * 验证验证码
	 * 
	 */
	public function checkVerify($mobile, $inputCode) {
		if (!$mobile || !$inputCode) return new PwError('USER:mobile.code.mobile.empty');
		$info = $this->_getDs()->getMobileVerify($mobile);
		if (!$info) return new PwError('USER:mobile.code.error');
		if ($info['expired_time'] < Pw::getTime()) return new PwError('USER:mobile.code.expired_time.error');
		if ($inputCode !== $info['code']) return new PwError('USER:mobile.code.error');
		// 手机验证通过后扩展
		PwSimpleHook::getInstance('PwMobileService_checkVerify')->runDo($mobile);
		return true;
	}

	/**
	 * 获取验证码
	 * 
	 */
	public function getVerify($mobile) {
		$code = $this->_buildCode(4);
		Wind::import('SRV:user.dm.PwUserMobileDm');
		$dm = new PwUserMobileDm();
		$dm->setMobile($mobile)
			->setCode($code);
		
		$this->_getDs()->addMobileVerify($dm);
		return $code;
	}

	/**
	 * 用户连续天数的行为记录&&用户累计行为记录
	 * 
	 * @param int $uid
	 * @param string $behavior 行为标记
	 * @param int $time 当前时间，为0则为累计行为记录,否则为连续行为记录(每天)
	 */
	public function replaceBehavior(PwUserMobileDm $dm) {
		$mobile = $dm->getField('mobile');
		$number = $this->checkTodayNum($mobile);
		if ($number instanceof PwError) {
			return $number;
		}
		$dm->setNumber($number);
		return $this->_getDs()->addMobileVerify($dm);
	}
	
	public function checkTodayNum($mobile) {
		$info = $this->_getDs()->getMobileVerify($mobile);
		$number = 1;
		$tdtime = Pw::getTdtime();
		if ($info) {
			$number = $info['number'];
			if ($info['create_time'] < $tdtime + 86400 && $info['create_time'] > $tdtime) {
				$number++;
			} else {
				$number = 1;
			}
		}
		if ($number > $this->sendNumDay) {
			return new PwError('USER:mobile.code.send.num.error');
		}
		return $number;
	}
	
	private function _buildCode($len = 4) {
		$str = '123456789';
		$_tmp = Pw::strlen($str)-1;
		$code = '';
	    $_num = 0;
	    for($i = 0;$i < $len;$i++){
	        $_num = mt_rand(0, $_tmp);
	        $code .= $str[$_num]; 
	    }
		return $code;
	}
	
	protected function _buildContent($code) {
		$search = array('{mobilecode}', '{sitename}');
		$replace = array($code, Wekit::C('site', 'info.name'));
		$content = str_replace($search, $replace, Wekit::C('register', 'mobile.message.content'));
		return $content;
	}
	
	/**
	 * 设置平台类型
	 */
	public function setPlat() {
		$this->plat = Wind::getComponent('mobileplat');
	}
	
	/**
	 * @return PwUserMobileVerify
	 */
	protected function _getDs() {
		return Wekit::load('user.PwUserMobileVerify');
	}
}