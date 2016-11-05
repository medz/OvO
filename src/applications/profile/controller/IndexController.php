<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:user.srv.PwUserProfileService');
Wind::import('SRV:user.validator.PwUserValidator');
Wind::import('SRV:user.PwUserBan');
Wind::import('APPS:profile.service.PwUserProfileExtends');
		
/**
 * 用户资料页面
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: IndexController.php 28946 2013-05-31 04:59:50Z jieyin $
 * @package src.products.u.controller.profile
 */
class IndexController extends BaseProfileController {
	
	/* (non-PHPdoc)
	 * @see BaseProfileController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setCurrentLeft('profile');
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$userInfo = Wekit::load('user.PwUser')->getUserByUid($this->loginUser->uid, PwUser::FETCH_INFO);
		$userInfo = array_merge($this->loginUser->info, $userInfo);
		list($year, $month, $day) = PwUserHelper::getBirthDay();
		
		$this->setOutput($this->_buildArea($userInfo['location']), 'location');
		$this->setOutput($this->_buildArea($userInfo['hometown']), 'hometown');
		
		$isAllowSign = false;
		if ($this->loginUser->getPermission('allow_sign')) {
			$isAllowSign = true;
			$isSignBan = false;
			if (Pw::getstatus($this->loginUser->info['status'], PwUser::STATUS_BAN_SIGN)) {
				Wind::import('SRV:user.srv.PwBanBp');
				$banBp = new PwBanBp($this->loginUser->uid);
				if (false === $banBp->checkIfBanSign()) {
					$banBp->recoveryBanSignError();
				} elseif ($banBp->endDateTimeBanSign()) {
					$s = 1 << (PwUser::STATUS_BAN_SIGN - 1);
					$this->loginUser->info['status'] = $this->loginUser->info['status'] - $s;
				} else {
					$isSignBan = true;
				}
			}
		}
		$extendsSrv = new PwUserProfileExtends($this->loginUser);
		list($_left, $_tab) = $this->getMenuService()->getCurrentTab($this->getInput('_left'), $this->getInput('_tab'));
		$extendsSrv->setCurrent($_left, $_tab);
		$this->runHook('c_profile_foot_run', $extendsSrv);
		$this->setOutput($extendsSrv, 'hookSrc');
		
		$this->setOutput($isAllowSign, 'isAllowSign');
		$this->setOutput($isSignBan, 'isSignBan');
		$this->setOutput($this->loginUser->getPermission('sign_max_length'), 'signMaxLength');
		$this->setOutput($year, 'years');
		$this->setOutput($month, 'months');
		$this->setOutput($day, 'days');
		$this->setOutput($userInfo, 'userinfo');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:profile.index.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/** 
	 * 编辑用户信息
	 */
	public function dorunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$userDm = new PwUserInfoDm($this->loginUser->uid);
		$userDm->setRealname($this->getInput('realname', 'post'));
		$userDm->setByear($this->getInput('byear', 'post'));
		$userDm->setBmonth($this->getInput('bmonth', 'post'));
		$userDm->setBday($this->getInput('bday', 'post'));
		$userDm->setGender($this->getInput('gender', 'post'));
		$userDm->setHomepage($this->getInput('homepage', 'post'));
		$userDm->setProfile($this->getInput('profile', 'post'));
		
		list($hometown, $location) = $this->getInput(array('hometown', 'location'), 'post');

		$srv = WindidApi::api('area');
		$areas = $srv->fetchAreaInfo(array($hometown, $location));
		$userDm->setHometown($hometown, isset($areas[$hometown]) ? $areas[$hometown] : '');
		$userDm->setLocation($location, isset($areas[$location]) ? $areas[$location] : '');
		
		//没有禁止签名的时候方可编辑签名
		if ($this->loginUser->getPermission('allow_sign')) {
			$bbsSign = $this->getInput('bbs_sign', 'post');
			if (($len = $this->loginUser->getPermission('sign_max_length')) && Pw::strlen($bbsSign) > $len) { //仅在此限制签名字数
				$this->showError(array('USER:user.edit.sign.length.over', array('{max}' => $len)));
			}
			Wind::import('LIB:ubb.PwUbbCode');
			Wind::import('LIB:ubb.config.PwUbbCodeConvertConfig');
			$ubb = new PwUbbCodeConvertConfig();
			$ubb->isConverImg = $this->loginUser->getPermission('sign_ubb_img') ? true : false;
			$userDm->setBbsSign($bbsSign)
				->setSignUseubb($bbsSign != PwUbbCode::convert($bbsSign, $ubb) ? 1 : 0);
		}
		
		$result = $this->_editUser($userDm, PwUser::FETCH_MAIN + PwUser::FETCH_INFO);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		} else {
			$this->loginUser->info = array_merge($this->loginUser->info, $userDm->getData());
			$this->showMessage('USER:user.edit.profile.success');
		}
	}
	
	/**
	 * 联系方式
	 */
	public function contactAction() {
		$userInfo = Wekit::load('user.PwUser')->getUserByUid($this->loginUser->uid, PwUser::FETCH_INFO);
		$extendsSrv = new PwUserProfileExtends($this->loginUser);
		list($_left, $_tab) = $this->getMenuService()->getCurrentTab($this->getInput('_left'), $this->getInput('_tab'));
		$extendsSrv->setCurrent($_left, $_tab);
		$this->runHook('c_profile_foot_run', $extendsSrv);
		$this->setOutput($extendsSrv, 'hookSrc');
		$this->setOutput($userInfo, 'userinfo');
	}
	
	/** 
	 * 编辑联系方式
	 */
	public function docontactAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$userDm = new PwUserInfoDm($this->loginUser->uid);
		$userDm->setTelphone($this->getInput('telphone', 'post'));
		$userDm->setAddress($this->getInput('address', 'post'));
		$userDm->setZipcode($this->getInput('zipcode', 'post'));
		$userDm->setAliww($this->getInput('aliww', 'post'));
		$userDm->setQq($this->getInput('qq', 'post'));
		$userDm->setMsn($this->getInput('msn', 'post'));
		list($alipay, $mobile) = $this->getInput(array('alipay', 'mobile'), 'post');
		if ($alipay) {
			$r = PwUserValidator::isAlipayValid($alipay, $this->loginUser->username);
			if ($r instanceof PwError) $this->showError($r->getError());
		}
		if ($mobile) {
			$r = PwUserValidator::isMobileValid($mobile);
			if ($r instanceof PwError) $this->showError($r->getError());
		}
		if ($email) {
			$r = PwUserValidator::isEmailValid($email, $this->loginUser->username);
			if ($r instanceof PwError) $this->showError($r->getError());
		}
		$userDm->setMobile($mobile);
		$userDm->setAlipay($alipay);
		$result = $this->_editUser($userDm, PwUser::FETCH_MAIN + PwUser::FETCH_INFO);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		} else {
			$this->loginUser->info = array_merge($this->loginUser->info, $userDm->getData());
			$this->showMessage('USER:user.edit.contact.success');
		}
	}
	
	/** 
	 * 密码验证
	 */
	public function editemailAction() {
		$userInfo = Wekit::load('user.PwUser')->getUserByUid($this->loginUser->uid, PwUser::FETCH_MAIN);
		$this->setOutput($userInfo, 'userinfo');
	}
	
	/** 
	 * 密码验证
	 */
	public function doeditemailAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($passwd, $email) = $this->getInput(array('passwd', 'email'), 'post');
		if (!$passwd || !$email) $this->showError('USER:empty.error');
		Wind::import('SRV:user.srv.PwTryPwdBp');
		$tryPwdBp = new PwTryPwdBp();
		if (($result = $tryPwdBp->checkPassword($this->loginUser->uid, $passwd, $this->getRequest()->getClientIp())) instanceof PwError) {
			list($error,) = $result->getError();
			if ($error == 'USER:login.error.pwd') {
				$this->showError($result->getError());
			} else {
				Wind::import('SRC:service.user.srv.PwUserService');
				$srv = new PwUserService();
				$srv->logout();
				$this->forwardAction('u/login/run', array('backurl' => WindUrlHelper::createUrl('profile/index/run')));
			}
		}
		$userDm = new PwUserInfoDm($this->loginUser->uid);
		$r = PwUserValidator::isEmailValid($email, $this->loginUser->username);
		if ($r instanceof PwError) $this->showError($r->getError());
		$userDm->setEmail($email);
		$result = $this->_editUser($userDm, PwUser::FETCH_MAIN);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		} else {
			$this->loginUser->info = array_merge($this->loginUser->info, $userDm->getData());
			$this->showMessage('USER:user.edit.contact.success', 'profile/index/contact?_tab=contact');
		}
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseController::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$this->setTemplate('profile_' . $handlerAdapter->getAction());
	}
	
	/**
	 * 编辑用户
	 *
	 * @param PwUserInfoDm $dm
	 * @param int $type
	 * @return boolean|PwError
	 */
	private function _editUser($dm, $type = PwUser::FETCH_MAIN) {
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$result = $userDs->editUser($dm, $type);
		if ($result instanceof PwError) return $result;
		/*用户资料设置完成-基本资料-service钩子点:s_PwUserService_editUser*/
		PwSimpleHook::getInstance('profile_editUser')->runDo($dm);
		return true;
	}
	
	/**
	 * 设置地区显示
	 * 
	 * @return array
	 */
	private function _buildArea($areaid) {
		$default = array(array('areaid' => '', 'name' => ''), array('areaid' => '', 'name' => ''), array('areaid' => '', 'name' => ''));
		if (!$areaid) {
			return $default;
		}
		$rout = WindidApi::api('area')->getAreaRout($areaid);
		return WindUtility::mergeArray($default, $rout);
	}
}