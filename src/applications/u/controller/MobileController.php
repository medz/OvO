<?php

/**
 * 手机验证码
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class MobileController extends PwBaseController {

	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		
	}
	
	/**
	 * 验证手机验证码
	 */
	public function checkmobilecodeAction() {
		list($mobile, $mobileCode) = $this->getInput(array('mobile', 'mobileCode'), 'post');
		if (($result = $this->_getService()->checkVerify($mobile, $mobileCode)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('fail');
	}
	
	/**
	 * PwMobileService
	 *
	 * @return PwMobileService
	 */
	private function _getService() {
		return Wekit::load('mobile.srv.PwMobileService');
	}
}