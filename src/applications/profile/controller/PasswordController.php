<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:user.validator.PwUserValidator');
/**
 * 用户密码设置
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PasswordController.php 24759 2013-02-20 07:02:37Z jieyin $
 * @package src.products.u.controller.profile
 */
class PasswordController extends BaseProfileController {
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$resource = Wind::getComponent('i18n');
		list($_pwdMsg, $_pwdArgs) = PwUserValidator::buildPwdShowMsg();
		$this->setOutput($resource->getMessage($_pwdMsg, $_pwdArgs), 'pwdReg');
		$this->setCurrentLeft('password');
		$this->appendBread('修改密码', WindUrlHelper::createUrl('profile/password/run'));
		$this->setTemplate('profile_password');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:profile.password.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}
	
	/** 
	 * 修改密码
	 */
	public function editAction() {
		//创始人不允许在前台修改密码
/*		if (Wekit::load('ADMIN:service.srv.AdminFounderService')->isFounder($this->loginUser->username)) {
			$this->showError('USER:founder.edit');
		}
		*/
		list($newPwd, $oldPwd, $rePwd) = $this->getInput(array('newPwd', 'oldPwd', 'rePwd'), 'post');
		if (!$oldPwd) {
			$this->showError('USER:pwd.change.oldpwd.require');
		}
		if (!$newPwd) {
			$this->showError('USER:pwd.change.newpwd.require');
		}
		if ($rePwd != $newPwd) {
			$this->showError('USER:user.error.-20');
		}
		
		$this->checkOldPwd($this->loginUser->uid, $oldPwd);

		$userDm = new PwUserInfoDm($this->loginUser->uid);
		$userDm->setPassword($newPwd);
		$userDm->setOldPwd($oldPwd);
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		if (($result = $userDs->editUser($userDm, PwUser::FETCH_MAIN)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->loginUser->reset();
		$this->showMessage('USER:pwd.change.success', 'profile/password/run?_type=2');
	}
	
	/**
	 * 设置安全问题
	 */
	public function questionAction() {
		/* @var $userSrv PwUserService */
		$userSrv = Wekit::load('SRV:user.srv.PwUserService');
		$this->setCurrentLeft('password');
		$this->setOutput($userSrv->isSetSafecv($this->loginUser->uid), 'isSetSafeQ');
		$this->setOutput(PwUserHelper::getSafeQuestion(), 'safeQuestionList');
		$this->appendBread('安全问题设置', WindUrlHelper::createUrl('profile/password/question'));
		$this->setTemplate('profile_question');
	}
	
	/**
	 * 设置安全问题
	 */
	public function dosetQAction() {
		list($oldPwd, $question, $answer) = $this->getInput(array('oldPwd', 'question', 'answer'), 'post');
		if (!$oldPwd) {
			$this->showError('USER:pwd.error');
		}
		
		$this->checkOldPwd($this->loginUser->uid, $oldPwd);

		$userDm = new PwUserInfoDm($this->loginUser->uid);
		$userDm->setOldPwd($oldPwd);
		
		switch ($question) {
			case -2://取消安全问题和答案
			case -3://无安全问题
				$question = $answer = '';
				$userDm->setQuestion('', '');
				break;
			case -4://自定义安全问题
				$myquestion = $this->getInput('myquestion', 'post');
				if (!$myquestion || !$answer) $this->showError('USER:login.question.setting');
				$userDm->setQuestion($myquestion, $answer);
				break;
			case -1://不修改安全问题和答案
//				$this->showMessage('USER:pwd.change.success', 'profile/password/question');
				break;
			default :
				if (!$answer) $this->showError('USER:login.question.setting.answer.require');
				$userDm->setQuestion($question, $answer);
				break;
		}
		
		/* @var $userService PwUserService */
		$userService = Wekit::load('user.srv.PwUserService');
		//如果该用户必须设置安全问题
		if ($userService->mustSettingSafeQuestion($this->loginUser->uid)) {
			if (!$question || ($question == -1 && !$userService->isSetSafecv())) {
				$this->showError('USER:user.error.safequestion.need');
			}
		}
		
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		if (($result = $userDs->editUser($userDm, PwUser::FETCH_MAIN)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->loginUser->reset();
		$this->showMessage('USER:login.question.setting.success', 'profile/password/question');
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
	 * 密码校验
	 */
	public function checkpwdAction() {
		$pwd = $this->getInput('pwd', 'post');
		$result = PwUserValidator::isPwdValid($pwd, $this->loginUser->username);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->addMessage(PwUserHelper::checkPwdStrong($pwd), 'rank');
		$this->showMessage();
	}
	
	/**
	 * 检查原密码
	 */
	public function checkOldPwdAction() {
		$pwd = $this->getInput('pwd', 'post');
		$this->checkOldPwd($this->loginUser->uid, $pwd);
		$this->showMessage();
	}

	/**
	 * 检查原密码
	 * @param int $uid
	 * @param string $pwd
	 * @return PwError|true
	 */
	private function checkOldPwd($uid, $pwd) {
		Wind::import('SRV:user.srv.PwTryPwdBp');
		$userSrv = new PwTryPwdBp();
		if (($r = $userSrv->checkPassword($uid, $pwd, $this->getRequest()->getClientIp())) instanceof PwError) {
			$refer = '';
			$error = $r->getError();
			$msg = is_array($error) ? $error[0] : $error;
			switch($msg) {
				case 'USER:login.error.tryover.pwd':
					$error[0] = 'USER:pwd.error.tryover';
					$this->loginUser->reset();
					$refer = 'u/login/logout';
					break;
				case 'USER:login.error.pwd':
					$error[0] = 'USER:pw.error.limit';
					break;
				default:
					break;
			}
			$this->showError($error, $refer);
		}
		return true;
	}
	
	private function _getWindid() {
		return WindidApi::api('user');
	}
}