<?php
/**
 * 验证码服务
 * 
 * 验证验证码：Wekit::load('verify.src.PwCheckVerifyService')->checkVerify($input);
 * 显示验证码： <div class="J_verify_code"></div>
 * 
 * 验证码类型的扩展实现：
 * 1.codetype里增加扩展类型
 * 2.实现getVerify()和checkVerify方法
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwVerifyService.php 23350 2013-01-09 02:40:57Z gao.wanggao $ 
 * @package 
 */

class PwVerifyService {
	
	/**
	 * 返回验证码类型
	 *
	 * @return array
	 */
	public function getVerifyType() {
		$conf = Wind::getRealPath('WINDID:service.verify.codetype.verify.php', true);
		$tmp = array('name' => '', 'alias' => '', 'description' => '', 'components' => array());
		$verify = @include $conf;

		return $verify;
	}

	public function getVerify($verifyType) {
		
		$types = $this->getVerifyType();
		if (!array_key_exists($verifyType, $types)) return new PwError('operate.fail');
		$verify = $types[$verifyType];
		if (!isset($verify['components']['path'])) return new PwError('operate.fail');
		$obj = Wekit::load($verify['components']['path']);
		return $obj->getVerify();
	}
	
	public function getOutType($verifyType) {
		$types = $this->getVerifyType();
		if (!array_key_exists($verifyType, $types)) return new PwError('operate.fail');
		$verify = $types[$verifyType];
		if (!isset($verify['components']['path'])) return new PwError('operate.fail');
		return $verify['components']['display'];
	}
	
	public function checkVerify($verifyType, $code = '') {
		var_dump($verifyType);
		if ($code == '')  return false;
		$types = $this->getVerifyType();
		if (!array_key_exists($verifyType, $types)) return false;
		$verify = $types[$verifyType];
		if (!isset($verify['components']['path'])) return false;
		$obj = Wekit::load($verify['components']['path']);
		if ($obj->checkVerify($code) === true ) return true;
		return false;
	}
}

?>