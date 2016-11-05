<?php
Wind::import('APPS:u.service.helper.PwUserHelper');
/**
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com> 
 * @link http://www.phpwind.com
 * @copyright Copyright ©2003-2010 phpwind.com
 * @license
 */

class UErrorController extends PwErrorController {
	/** 
	 * 用户注册信息错误
	 * 
	 * @return void
	 */
	public function regErrorAction() {
		if (in_array('register', Wekit::C('verify', 'showverify'))) {
			$this->setOutput('verify');
		}
		$config = Wekit::C('register');
		$this->setOutput($config, 'config');
		$this->setOutput(PwUserHelper::getRegFieldsMap(), 'needFields');
		$this->setOutput(array('location', 'hometown'), 'selectFields');
		$this->setOutput($this->error, 'message');
		$userForm = $this->getInput('pwUserRegisterForm');
		$this->setOutput($userForm->getData(), 'data');
		$this->setOutput($this->state, 'state');
		$this->setTemplatePath('TPL:u');
		$this->setTemplateExt('htm');
		$this->setTemplate('register');
	}
	
	/** 
	 * 用户注册信息错误
	 * 
	 * @return void
	 */
	public function loginErrorAction() {
		$this->setOutput($this->state, 'state');
		$this->setOutput($this->error, 'message');
		$userForm = $this->getInput('pwUserLoginForm');
		$this->setOutput($userForm->getData(), 'data');
		$this->setTemplatePath('TPL:u');
		$this->setTemplateExt('htm');
		$this->setTemplate('login');
	}
}