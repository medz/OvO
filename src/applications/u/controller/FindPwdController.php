<?php
Wind::import('SRV:user.srv.PwFindPassword');
Wind::import('APPS:u.service.helper.PwUserHelper');
Wind::import('SRV:user.validator.PwUserValidator');
/**
 * 重置密码流程
 * 重置成功一次  才算找回密码次数完成一次，才会更新验证码状态及找回密码次数
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: FindPwdController.php 22230 2012-12-19 21:45:20Z xiaoxia.xuxx $
 * @package src.products.user.controller
 */
class FindPwdController extends PwBaseController {
	private $isMailOpen = false;
	private $isMobileOpen = false;

	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		if ($this->loginUser->isExists()) $this->forwardRedirect(WindUrlHelper::createUrl('bbs/index/run'));
		$this->isMailOpen = Wekit::C('email', 'mailOpen') ? true : false;
		$this->isMobileOpen = Wekit::C('login', 'mobieFindPasswd') ? true : false;
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		if (false === ($this->isMailOpen || $this->isMobileOpen)) {
			$this->setTemplate('findpwd_close');
		}
	}
	
	/**
	 * 检查用户密码
	 */
	public function checkUsernameAction() {
		$username = $this->getInput('username', 'post');
		if (!$username) {
			$this->showError('USER:findpwd.username.require', 'u/findPwd/run');
		}
		
		/*用户不存在*/
		if (!PwUserValidator::checkUsernameExist($username)) $this->showError('USER:user.error.-14');
		$findPasswordBp = new PwFindPassword($username);
		
		/*[用户分支1：没有绑定任何可以找回密码的方式]*/
		if (false === ($findPasswordBp->isBindMail() || $findPasswordBp->isBindMobile())) {
			$this->showError('USER:findpwd.notbind');
		}
		$isOverMail = $findPasswordBp->isOverByMail();
		$isOverMobile = $findPasswordBp->isOverByMobile();
		/*[用户分支2：两种方式的找回密码都已经超过当日次数限制]*/
		if ($isOverMail && $isOverMobile) {
			$this->showError('USER:findpwd.over.limit');
		}
		
		/*【分支1：只开通手机】网站开通了：手机找回密码方式*/
		if (false === $this->isMailOpen && $this->isMobileOpen) {
			if ($isOverMobile) $this->showError('USER:findpwd.over.limit.mobile');
			if ($this->getInput('step', 'post') == 'do') {
				$this->forwardAction('u/findPwd/bymobile?username=' . $username);
			}
			$this->showMessage();
		}
		/*【分支2：只开通邮箱】网站开通了：邮箱找回密码方式*/
		if (false === $this->isMobileOpen && $this->isMailOpen) {
			if ($isOverMail) $this->showError('USER:findpwd.over.limit.email');
			if ($this->getInput('step', 'post') == 'do') {
				$this->forwardAction('u/findPwd/bymail?username=' . $username);
			}
			$this->showMessage();
		}
		/*【分支3：都关闭】网站关闭找回密码方式*/
		if (false === ($this->isMobileOpen || $this->isMailOpen)) {
			$this->showError('USER:findpwd.way.close');
			if ($this->getInput('step', 'post') == 'do') {
				$this->forwardAction('u/findPwd/bymail?username=' . $username);
			}
			$this->showMessage();
		}
		
		/*【分支4：都开通】网站开通了：手机找回密码和邮箱找回密码方式*/
		/*[分支4.1：用户只绑定了手机找回密码方式]*/
		if (false === $findPasswordBp->isBindMail() && $findPasswordBp->isBindMobile()) {
			if ($isOverMobile) {
				$this->showError('USER:findpwd.over.limit.mobile');
			}
			if ($this->getInput('step', 'post') == 'do') {
				$this->forwardAction('u/findPwd/bymobile?username=' . $username);
			}
			$this->showMessage();
		}
		/*[分支4.2： 用户只绑定了邮箱找回密码方式]*/
		if (false === $findPasswordBp->isBindMobile() && $findPasswordBp->isBindMail()) {
			if ($isOverMail) {
				$this->showError('USER:findpwd.over.limit.email');
			}
			if ($this->getInput('step', 'post') == 'do') {
				$this->forwardAction('u/findPwd/bymail?username=' . $username);
			}
			$this->showMessage();
		}
		
		/*[分支4.3：用户都绑定了两种方式]*/
		/*网站支持两种方式找回密码*/
		if ($this->getInput('step', 'post') == 'do') {
			$this->setOutput($username, 'username');
			$this->setTemplate('findpwd_way');
		} else {
			$this->showMessage('');
		}
	}
	
	/**
	 * 通过邮箱找回密码
	 */
	public function bymailAction() {
		$username = $this->getInput('username');
		if (!$username) {
			$this->showError('USER:findpwd.username.require', 'u/findPwd/run');
		}
		if (!$this->isMailOpen) {
			$this->showError('USER:findpwd.way.email.close', 'u/findPwd/run');
		}
		$findPasswordBp = new PwFindPassword($username);
		$this->setOutput($findPasswordBp->getFuzzyEmail(), 'mayEmail');
		$this->setOutput(in_array('resetpwd', Wekit::C('verify', 'showverify')), 'verify');
		$this->setOutput($username, 'username');
		$this->setOutput(2, 'step');
	}
	
	/**
	 * 检查有效是否正确
	 */
	public function dobymailAction() {
		if (!$this->isMailOpen) {
			$this->showError('USER:findpwd.way.email.close', 'u/findPwd/run');
		}
		list($username, $email, $code) = $this->getInput(array('username', 'email', 'code'), 'post');
		$this->checkCode($code);
		/*检查邮箱是否正确*/
		$findPasswordBp = new PwFindPassword($username);
		if (true !== ($result = $findPasswordBp->checkEmail($email))) {
			$this->showError($result->getError());
		}
		/*发送重置邮件*/
		if (!$findPasswordBp->sendResetEmail(PwFindPassword::createFindPwdIdentify($username, PwFindPassword::WAY_EMAIL, $email))) {
			$this->showError('USER:findpwd.error.sendemail');
		}
		
		$this->setOutput($username, 'username');
		$this->setOutput($findPasswordBp->getEmailUrl(), 'emailUrl');
		$this->setOutput(3, 'step');
		$this->setTemplate('findpwd_bymail');
	}
	
	/**
	 * 通过手机号码找回密码
	 */
	public function bymobileAction() {
		$username = $this->getInput('username');
		if (!$username) {
			$this->showError('USER:findpwd.username.require', 'u/findPwd/run');
		}
		if (!$this->isMobileOpen) {
			$this->showError('USER:findpwd.way.mobile.close', 'u/findPwd/run');
		}
		$this->setOutput(in_array('resetpwd', Wekit::C('verify', 'showverify')), 'verify');
		$this->setOutput($username, 'username');
		$this->setOutput(2, 'step');
	}
	
	/**
	 * 验证手机验证码
	 */
	public function checkmobilecodeAction() {
		if (!$this->isMobileOpen) {
			$this->showError('USER:findpwd.way.mobile.close', 'u/findPwd/run');
		}
		list($username, $mobileCode, $mobile) = $this->getInput(array('username', 'mobileCode', 'mobile'), 'post');
		!PwUserValidator::isMobileValid($mobile) && $this->showError('USER:error.mobile', 'u/findPwd/run');
		!$mobileCode && $this->showError('USER:mobile.code.empty', 'u/findPwd/run');

		$userInfo = $this->_getUserDs()->getUserByName($username, PwUser::FETCH_INFO);
		if ($userInfo['mobile'] != $mobile) {
			$this->showError('USER:findpwd.error.mobile');
		}
		if (($mobileCheck = Wekit::load('mobile.srv.PwMobileService')->checkVerify($mobile, $mobileCode)) instanceof PwError) {
			$this->showError($mobileCheck->getError());
		}
		
		$statu = PwFindPassword::createFindPwdIdentify($username, PwFindPassword::WAY_MOBILE, $mobile);
		$this->showMessage('success','u/findPwd/resetpwd?way=mobile&_statu='.$statu.'&mobile='.$mobile.'&mobileCode='.$mobileCode);
	}

	/**
	 * 验证邮件展示重置密码页面
	 */
	public function resetpwdAction() {
		list($userinfo, $value, $type, $statu) = $this->checkState();
		$code = $this->getInput('code', 'get');
		$findPasswordBp = new PwFindPassword($userinfo['username']);
		if ($type == PwFindPassword::WAY_EMAIL) {
			if ($findPasswordBp->isOverByMail()) {
				$this->showError('USER:findpwd.over.limit.email');
			}
			if (($result = $findPasswordBp->checkResetEmail($value, $code)) instanceof PwError) {
				$this->showError($result->getError());
			}
		}
		if ($type == PwFindPassword::WAY_MOBILE) {
			if ($findPasswordBp->isOverByMobile()) {
				$this->showError('USER:findpwd.over.limit.mobile');
			}
			list($mobile, $mobileCode) = $this->getInput(array('mobile', 'mobileCode'), 'get');
			if (($mobileCheck = Wekit::load('mobile.srv.PwMobileService')->checkVerify($mobile, $mobileCode)) instanceof PwError) {
				$this->showError($mobileCheck->getError());
			}
		}
		$resource = Wind::getComponent('i18n');
		list($_pwdMsg, $_pwdArgs) = PwUserValidator::buildPwdShowMsg();
		$this->setOutput($resource->getMessage($_pwdMsg, $_pwdArgs), 'pwdReg');
		$this->setOutput($userinfo['username'], 'username');
		$this->setOutput($statu, 'statu');
		$this->setTemplate('findpwd_resetpwd');
	}
	
	/**
	 * 重置密码
	 */
	public function doresetpwdAction() {
		if ($this->getInput('step', 'post') == 'end') {
			list($userInfo, $value, $type) = $this->checkState();
			list($password, $repassword) = $this->getInput(array('password', 'repassword'), 'post');
			if ($password != $repassword) $this->showError('USER:user.error.-20');
			$userDm = new PwUserInfoDm($userInfo['uid']);
			$userDm->setUsername($userInfo['username']);
			$userDm->setPassword($password);
			$userDm->setQuestion('', '');
			/* @var $userDs PwUser */
			$userDs = Wekit::load('user.PwUser');
			$result = $userDs->editUser($userDm, PwUser::FETCH_MAIN);
			if ($result instanceof PwError) {
				$this->showError($result->getError());
			} else {
				//检查找回密码次数及更新
				$findPasswordBp = new PwFindPassword($userInfo['username']);
				$findPasswordBp->success($type);
			}
			$this->showMessage('USER:findpwd.success', 'u/login/run?backurl=' . WindUrlHelper::createUrl('bbs/index/run'));
		}
	}
	
	/**
	 * 发送手机验证码
	 */
	public function sendmobileAction() {
		list($mobile, $username) = $this->getInput(array('mobile', 'username'), 'post');
		if (($result = $this->_checkMobileRight($mobile, $username)) instanceof PwError) {
			$this->showError($result->getError());
		}
		if (($result = Wekit::load('SRV:mobile.srv.PwMobileService')->sendMobileMessage($mobile)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('success');
	}
	
	/**
	 * 验证手机号码
	 */
	public function checkmobileAction() {
		list($mobile, $username) = $this->getInput(array('mobile', 'username'), 'post');
		if (($result = $this->_checkMobileRight($mobile, $username)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$result = Wekit::load('SRV:mobile.srv.PwMobileService')->checkTodayNum($mobile);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage();
	}
	
	private function _checkMobileRight($mobile, $username) {
		if (!$this->isMobileOpen) {
			return new PwError('USER:mobile.findPwd.open.error');
		}
		Wind::import('SRV:user.validator.PwUserValidator');
		if (!PwUserValidator::isMobileValid($mobile)) {
			return new PwError('USER:error.mobile');
		}
		$userInfo = $this->_getUserDs()->getUserByName($username, PwUser::FETCH_INFO);
		if ($userInfo['mobile'] != $mobile) {
			return new PwError('USER:findpwd.error.mobile');
		}
		return true;
	}
	
	/**
	 * 检查邮箱地址合法性
	 */
	public function checkMailFormatAction() {
		if (!WindValidator::isEmail($this->getInput('email', 'post'))) {
			$this->showError('USER:user.error.-7');
		} else {
			$this->showMessage();
		}
	}
	
	/**
	 * 检查手机号码格式是否正确
	 */
	public function checkPhoneFormatAction() {
		if (!PwUserValidator::isMobileValid($this->getInput('phone', 'post'))) {
			$this->showError('USER:mobile.error.formate');
		} else {
			$this->showMessage();
		}
	}
	
	/**
	 * 检查是否符合要求
	 * @param string $type 类型
	 */
	private function checkState() {
		$statu = $this->getInput('_statu', 'get');
		!$statu && $statu = $this->getInput('statu', 'post');
		if (!$statu) $this->showError('USER:illegal.request');
		list($username, $way, $value) = PwFindPassword::parserFindPwdIdentify($statu);
		$userInfo = $this->_getUserDs()->getUserByName($username, PwUser::FETCH_INFO | PwUser::FETCH_MAIN);
		if ($userInfo[PwFindPassword::getField($way)] != $value) {
			$this->forwardAction('u/findPwd/run', array(), true);
		}
		return array($userInfo, $value, $way, $statu);
	}
	
	/**
	 * 检查验证码
	 *
	 * @param string $code
	 * @return boolean
	 */
	private function checkCode($code) {
		if (!in_array('resetpwd', Wekit::C('verify', 'showverify'))) return true;
		/*验证码检查*/
		/* @var $verifySrv PwCheckVerifyService */
		$verifySrv = Wekit::load("verify.srv.PwCheckVerifyService");
		if ($verifySrv->checkVerify($code) !== true) {
			$this->showError('USER:verifycode.error');
		}
		return true;
	}
	
	/**
	 * 获得用户的DS
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
	
	/* (non-PHPdoc)
	 * @see WindSimpleController::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$this->setTemplate(strtolower($handlerAdapter->getController()) . '_' . $handlerAdapter->getAction());
	}
}