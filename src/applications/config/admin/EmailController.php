<?php
Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台设置-站点设置-站点信息设置/全局参数设置
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-7
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: EmailController.php 3286 2011-12-15 09:32:42Z yishuo $
 * @package admin
 * @subpackage controller.config
 */
class EmailController extends AdminBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$config = $this->_getConfig();
		$this->setOutput($config, 'config');
		$t = Pw::strlen($config['mail.password']);
		$password = Pw::substrs($config['mail.password'], 1, 0, false) . '********' . Pw::substrs($config['mail.password'], 1, $t-1, false);
		$this->setOutput($password, 'password');
	}

	/**
	 * 后台设置-email设置
	 */
	public function dorunAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$password = $this->getInput('mailPassword', 'post');
		$config = $this->_getConfig();
		$t = Pw::strlen($config['mail.password']);
		$passwordO = Pw::substrs($config['mail.password'], 1, 0, false) . '********' . Pw::substrs($config['mail.password'], 1, $t-1, false);
		$password = $password == $passwordO ? $config['mail.password'] : $password;
		$config = new PwConfigSet('email');
		$config->set('mailOpen', $this->getInput('mailOpen', 'post'))
			->set('mailMethod', 'smtp')
			->set('mail.host', $this->getInput('mailHost', 'post'))
			->set('mail.port', $this->getInput('mailPort', 'post'))
			->set('mail.from', $this->getInput('mailFrom', 'post'))
			->set('mail.auth', $this->getInput('mailAuth', 'post'))
			->set('mail.user', $this->getInput('mailUser', 'post'))
			->set('mail.password', $password)
			->flush();
		$this->showMessage('ADMIN:success');
	}
	
	/**
	 * 发送测试邮件
	 */
	public function sendAction() {
		$config = $this->_getConfig();
		$this->setOutput($config['mail.from'], 'from');
	}
	
	/**
	 * 发送测试邮件
	 */
	public function dosendAction() {
		Wind::import('LIB:utility.PwMail');
		list($fromEmail, $toEmail) = $this->getInput(array('fromEmail', 'toEmail'), 'post');
		if (!$toEmail) $this->showError('ADMIN:email.test.toemail.require');
		$mail = new PwMail();
		$title = Wekit::C('site', 'info.name') . ' 测试邮件';
		$content = '恭喜您，如果您收到此邮件则代表后台邮件发送设置正确！';
		$result = $mail->sendMail($toEmail, $title, $content);
		if ($result === true) {
			$this->showMessage('ADMIN:email.test.success');
		}
		$i18n = Wind::getComponent('i18n');
		$this->showError(array('ADMIN:email.test.error', array('{error}' => $i18n->getMessage($result->getError()))));
	}
	
	/**
	 * 加载Config DS 服务
	 * 
	 * @return array
	 */
	private function _getConfig() {
		return Wekit::C()->getValues('email');
	}
}
