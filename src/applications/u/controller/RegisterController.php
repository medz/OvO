<?php
Wind::import('SRV:user.srv.PwRegisterService');
Wind::import('APPS:u.service.helper.PwUserHelper');
Wind::import('SRV:user.validator.PwUserValidator');

/**
 * 用户登录/注册controller
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: RegisterController.php 24493 2013-01-31 03:40:55Z jieyin $
 * @package src.products.u.controller
 */
class RegisterController extends PwBaseController {
	
	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setOutput('用户注册', 'title');
		$config = Wekit::C('register');
		if (0 == $config['type'] && ('close' != $handlerAdapter->getAction())) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/register/close'));
		}
	}
	
	/* (non-PHPdoc)
	 *  用户注册
	 * 如果开启同一个IP地址在一定时间内不能再次注册
	 * @see WindController::run()
	 */
	public function run() {
		$this->init();
		$this->setOutput($this->getInput('invite'), 'invite');
		$this->setOutput(WindUrlHelper::createUrl('bbs/index/run'), 'backurl');
		$this->setTemplate('register');
		
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:u.register.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/**
	 * 邀请码链接
	 */
	public function inviteAction() {
		$config = Wekit::C('register');
		if ($config['type'] != 2) $this->showError('USER:invite.close');
		$this->init();
		$inviteCode = $this->getInput('code', 'get');
		$this->setOutput($inviteCode, 'invitecode');
		$this->setTemplate('register');
	}
	
	/**
	 * 检查邀请码是否可以用
	 */
	public function checkInvitecodeAction() {
		$code = $this->getInput('invitecode', 'post');
		/* @var $inviteService PwInviteCodeService */
		$inviteService = Wekit::load('invite.srv.PwInviteCodeService');
		if (($info = $inviteService->allowUseInviteCode($code)) instanceof PwError) {
			$this->showError($info->getError());
		}
		$info = $this->_getUserDs()->getUserByUid($info['created_userid']);
		$this->showMessage(array('USER:invite.code.check.success', array('username' => $info['username'])));
	}
	
	/**
	 * 执行用户注册
	 */
	public function dorunAction() {
		$this->setOutput('注册', 'title');
		
		$registerService = new PwRegisterService();
		$registerService->setUserDm($this->_getUserDm());
		/*[u_regsiter]:插件扩展*/
		$this->runHook('c_register', $registerService);
		if (($info = $registerService->register()) instanceof PwError) {
			$this->showError($info->getError());
		} else {
			$identity = PwRegisterService::createRegistIdentify($info['uid'], $info['password']);
			if (1 == Wekit::C('register', 'active.mail')) {
				$this->forwardAction('u/register/sendActiveEmail', array('_statu' => $identity), true);
			} else {
				$this->forwardAction('u/register/welcome', array('_statu' => $identity), true);
			}
		}
	}

	/**
	 * 发送激活邮箱
	 */
	public function sendActiveEmailAction() {
		$statu = $this->checkRegisterUser();
		if (!Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNACTIVE)) {
			$this->setOutput('activeEmail', 'type');
			$this->setTemplate('register_about');
			return;
		}
		$registerService = new PwRegisterService();
		$info = $this->loginUser->info;
		if (false == $registerService->checkIfActiveEmailSend($info['uid'], $info['email'])) {
			$registerService->sendEmailActive($info['username'], $info['email'], $statu, $info['uid']);
		}
		
		$mailList = array('gmail.com' => 'google.com');
		list(, $mail) = explode('@', $info['email'], 2);
		$gotoEmail = 'http://mail.' . (isset($mailList[$mail]) ? $mailList[$mail] : $mail);
		
		$this->setOutput($info['email'], 'email');
		$this->setOutput($info['username'], 'username');
		$this->setOutput($gotoEmail , 'gotoEmail');
		$this->setOutput($statu, '_statu');
		$this->setOutput($this->getInput('from'), 'from');
		$this->setTemplate('register_emailactive');
	}
	
	/**
	 * 再次发送激活邮件
	 */
	public function sendActiveEmailAgainAction() {
		$_statu = $this->checkRegisterUser();
		if (!Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNACTIVE)) {
			$this->showMessage('USER:active.email.dumplicate');
		}
		$registerService = new PwRegisterService();
		$registerService->sendEmailActive($this->loginUser->info['username'], $this->loginUser->info['email']);
		$this->showMessage('USER:active.sendemail.success');
	}
	
	/**
	 * 更改邮箱
	 */
	public function editEmailAction() {
		$_statu = $this->checkRegisterUser();
		if (!Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNACTIVE)) {
			$this->showMessage('USER:active.email.dumplicate', 'u/login/run');
		}
		$email = $this->getInput('email', 'post');
		$result = PwUserValidator::isEmailValid($email, $this->loginUser->info['username']);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		} else {
			$userInfo = new PwUserInfoDm($this->loginUser->uid);
			$userInfo->setEmail($email);
			$this->_getUserDs()->editUser($userInfo, PwUser::FETCH_MAIN);
			$registerService = new PwRegisterService();
			$registerService->sendEmailActive($this->loginUser->info['username'], $email, $_statu, $this->loginUser->uid);
			$this->showMessage('USER:active.editemail.success', 'u/register/sendActiveEmail?_statu=' . $_statu);
		}
	}

	/**
	 * 激活邮箱链接
	 */
	public function activeEmailAction() {
		$_statu = $this->checkRegisterUser();
		if (!Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNACTIVE)) {
//			$this->showMessage('USER:active.email.success', 'u/register/welcome?_statu=' . $_statu);
			$this->setOutput('activeEmail', 'type');
			$this->setTemplate('register_about');
			return;
		}
		$code = $this->getInput('code');
		$PwUserRegisterBp = new PwRegisterService();
		$result = $PwUserRegisterBp->activeEmail($this->loginUser->uid, $this->loginUser->info['email'], $code);
		if ($result instanceof PwError) $this->showError($result->getError());
		
		//激活成功登录
		Wind::import('SRV:user.srv.PwLoginService');
		$login = new PwLoginService();
		$login->setLoginCookie($this->loginUser, $this->getRequest()->getClientIp());
		/* @var $guideService PwUserRegisterGuideService */
		$guideService = Wekit::load('APPS:u.service.PwUserRegisterGuideService');
		$this->setOutput($guideService->hasGuide(), 'goGuide');
		$this->setOutput('activeEmailSuccess', 'type');
		$this->setTemplate('register_about');
//		$this->showMessage('USER:active.email.success', 'u/register/welcome?_statu=' . $_statu);
	}
	
	/**
	 * 完成注册，显示欢迎信息
	 */
	public function welcomeAction() {
		if (!$this->getInput('_statu')) $this->forwardRedirect(WindUrlHelper::createUrl('u/register/run'));
		$statu = $this->checkRegisterUser();
		if (Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNACTIVE)) {
			$this->forwardAction('u/register/sendActiveEmail', array('_statu' => $statu), true);
		}
		Wind::import('SRV:user.srv.PwLoginService');
		$login = new PwLoginService();
		$login->setLoginCookie($this->loginUser, $this->getRequest()->getClientIp());
		
		$this->forwardRedirect(WindUrlHelper::createUrl('u/register/guide'));
	}
	
	/**
	 * 用户引导页面
	 *
	 */
	public function guideAction() {
		if (!$this->loginUser->isExists()) $this->forwardRedirect(Wekit::url()->base);
		$key = $this->getInput('key');
		/* @var $guideService PwUserRegisterGuideService */
		$guideService = Wekit::load('APPS:u.service.PwUserRegisterGuideService');
		$next = $guideService->getNextGuide($key);
		if (!$next) {
			if (Wekit::C('register', 'active.check')) {
				$this->setOutput(1, 'check');
				if (!Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_UNCHECK)) {
					$this->forwardRedirect(Wekit::url()->base);
				}
			}
			$synLogin = $this->_getWindid()->synLogin($this->loginUser->uid);
			$this->setOutput($this->loginUser->info['username'], 'username');
			$this->setOutput('success', 'type');
			$this->setOutput($synLogin, 'synLogin');
			$this->setTemplate('register_about');
		} else {
			$this->forwardRedirect(WindUrlHelper::createUrl($next['guide']));
		}
	}

	/**
	 * 检查邮箱唯一性
	 */
	public function checkemailAction() {
		list($email, $username) =$this->getInput(array('email', 'username'), 'post');
		$result = PwUserValidator::isEmailValid($email, $username);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage();
	}
	
	/**
	 * 检查用户名的唯一性
	 */
	public function checkusernameAction() {
		$username = $this->getInput('username', 'post');
		$result = PwUserValidator::isUsernameValid($username);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage();
	}

	/**
	 * 检查密码复杂度是否符合
	 */
	public function checkpwdAction() {
		list($pwd, $username) = $this->getInput(array('pwd', 'username'), 'post');
		$result = PwUserValidator::isPwdValid($pwd, $username);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->addMessage(PwUserHelper::checkPwdStrong($pwd), 'rank');
		$this->showMessage();
	}
	
	/**
	 * 检查密码强度
	 */
	public function checkpwdStrongAction() {
		$pwd = $this->getInput('pwd', 'post');
		$this->addMessage(PwUserHelper::checkPwdStrong($pwd), 'rank');
		$this->showMessage();
	}
	
	/**
	 * 发送手机验证码
	 */
	public function sendmobileAction() {
		$mobile = $this->getInput('mobile', 'post');
		if (($result = $this->_checkMobileRight($mobile)) instanceof PwError) {
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
		$mobile = $this->getInput('mobile', 'post');
		if (($result = $this->_checkMobileRight($mobile)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$result = Wekit::load('SRV:mobile.srv.PwMobileService')->checkTodayNum($mobile);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage();
	}
	
	private function _checkMobileRight($mobile) {
		$config = Wekit::C('register');
		if (!$config['active.phone']) {
			return new PwError('USER:mobile.reg.open.error');
		}
		Wind::import('SRV:user.validator.PwUserValidator');
		if (!PwUserValidator::isMobileValid($mobile)) {
			return new PwError('USER:error.mobile');
		}
		$mobileInfo = Wekit::load('user.PwUserMobile')->getByMobile($mobile);
		if ($mobileInfo) $this->showError('USER:mobile.mobile.exist');
		return true;
	}
	
	/**
	 * 验证用户标识
	 *
	 * @return string
	 */
	private function checkRegisterUser() {
		$identify = $this->getInput('_statu', 'get');
		!$identify && $identify = $this->getInput('_statu', 'post');
		if (!$identify) $this->showError('USER:illegal.request');
		list($uid, $password) = PwRegisterService::parserRegistIdentify($identify);
		$info = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_MAIN);
		if (Pw::getPwdCode($info['password']) != $password) {
			$this->showError('USER:illegal.request');
		}
		$this->loginUser = new PwUserBo($uid);
		return $identify;
	}
	
	/**
	 * 初始化
	 */
	private function init() {
		$registerService = new PwRegisterService();
		$result = $registerService->checkIp($this->getRequest()->getClientIp());
		if ($result instanceof PwError) $this->showMessage($result->getError());
		$resource = Wind::getComponent('i18n');
		list($_pwdMsg, $_pwdArgs) = PwUserValidator::buildPwdShowMsg();
		list($_nameMsg, $_nameArgs) = PwUserValidator::buildNameShowMsg();
		$this->setOutput($resource->getMessage($_pwdMsg, $_pwdArgs), 'pwdReg');
		$this->setOutput($resource->getMessage($_nameMsg, $_nameArgs), 'nameReg');
		$this->setOutput($this->_showVerify(), 'verify');
		$this->setOutput($this->_getRegistConfig(), 'config');
		$this->setOutput(PwUserHelper::getRegFieldsMap(), 'needFields');
		$this->setOutput(array('location', 'hometown'), 'areaFields');
	}

        
    /**
     * 判断是否需要展示验证码
     * @return boolean
     */
    private function _showVerify() {
        $config = Wekit::C('verify', 'showverify');
        !$config && $config = array();
        if(in_array('register', $config)==true){
            return true;
        }else{
            //ip限制,防止撞库; 错误三次,自动显示验证码
            $ipDs = Wekit::load('user.PwUserLoginIpRecode');
            $info = $ipDs->getRecode($this->getRequest()->getClientIp());
            return is_array($info) && $info['error_count']>3 ? true : false;
        }   
    }

	/**
	 * 关闭
	 */
	public function closeAction() {
		$config = Wekit::C('register');
		if ($config['type']) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/register/run'));
		}
		$this->setOutput($config['close.msg'], 'close');
		$this->setTemplate('register_close');
	}
	
	/**
	 * 获得用户DS
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
	
	private function _getWindid() {
		return WindidApi::api('user');
	}
	
	/**
	 * 获取注册的信息
	 *
	 * @return PwUserInfoDm
	 */
	private function _getUserDm() {
		list($username, $password, $repassword, $email, $aliww, $qq, $msn, $mobile, $mobileCode, $hometown, $location, $question, $answer, $regreason, $code) =
		$this->getInput(array('username', 'password', 'repassword', 'email', 'aliww', 'qq', 'msn', 'mobile', 'mobileCode', 'hometown', 'location', 'question', 'answer', 'regreason', 'code'),
			'post');
		
		//	验证输入
		Wind::import('Wind:utility.WindValidator');
		$config = $this->_getRegistConfig();
		if (!$username) $this->showError('USER:user.error.-1', 'u/register/run');
		if (!$password) $this->showError('USER:pwd.require', 'u/register/run');
		if (!$email) $this->showError('USER:user.error.-6', 'u/register/run');
		if (!WindValidator::isEmail($email)) $this->showError('USER:user.error.-7', 'u/register/run');
		
		foreach ($config['active.field'] as $field) {
			if (!$this->getInput($field, 'post')) $this->showError('USER:register.error.require.needField.' . $field, 'u/register/run');
		}
		if ($config['active.check'] && !$regreason) {
			$this->showError('USER:register.error.require.regreason', 'u/register/run');
		}
		if ($config['active.phone']) {
			!PwUserValidator::isMobileValid($mobile) && $this->showError('USER:error.mobile', 'u/register/run');
			if (($mobileCheck = Wekit::load('mobile.srv.PwMobileService')->checkVerify($mobile, $mobileCode)) instanceof PwError) {
				$this->showError($mobileCheck->getError());
			}
		}
		if ($repassword != $password) $this->showError('USER:user.error.-20', 'u/register/run');
		if (in_array('register', (array)Wekit::C('verify', 'showverify'))) {
			$veryfy = Wekit::load("verify.srv.PwCheckVerifyService");
			if (false === $veryfy->checkVerify($code)) $this->showError('USER:verifycode.error', 'u/register/run');
		}
		
		Wind::import('SRC:service.user.dm.PwUserInfoDm');
		$userDm = new PwUserInfoDm();
		$userDm->setUsername($username);
		$userDm->setPassword($password);
		$userDm->setEmail($email);
		$userDm->setRegdate(Pw::getTime());
		$userDm->setLastvisit(Pw::getTime());
		$userDm->setRegip(Wind::getComponent('request')->getClientIp());
		
		$userDm->setAliww($aliww);
		$userDm->setQq($qq);
		$userDm->setMsn($msn);
		$userDm->setMobile($mobile);
		$userDm->setMobileCode($mobileCode);
		$userDm->setQuestion($question, $answer);
		$userDm->setRegreason($regreason);
		
		$areaids = array($hometown, $location);
		if ($areaids) {
			$srv = WindidApi::api('area');
			$areas = $srv->fetchAreaInfo($areaids);
			$userDm->setHometown($hometown, isset($areas[$hometown]) ? $areas[$hometown] : '');
			$userDm->setLocation($location, isset($areas[$location]) ? $areas[$location] : '');
		}
		return $userDm;
	}
	
	/**
	 * 注册的相关配置
	 * 
	 * @return array
	 */
	private function _getRegistConfig() {
		$config = Wekit::C('register');
		!$config['active.field'] && $config['active.field'] = array();
		return $config;
	}
	
}
