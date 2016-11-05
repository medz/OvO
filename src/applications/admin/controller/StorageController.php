<?php

Wind::import('ADMIN:library.AdminBaseController');

/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-12-15
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: StorageController.php 28785 2013-05-23 09:54:16Z jieyin $
 * @package admin
 * @subpackage controller.config
 */
class StorageController extends AdminBaseController {
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		
	}

	/**
	 * 后台设置-ftp设置
	 */
	public function ftpAction() {
		$config = Wekit::C()->getValues('attachment');
		$this->setOutput($config, 'config');
	}

	/**
	 * 后台设置-ftp设置
	 */
	public function doftpAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$config = new PwConfigSet('attachment');
		$config->set('ftp.url', $this->getInput('ftpUrl', 'post'))
			->set('ftp.server', $this->getInput('ftpServer', 'post'))
			->set('ftp.port', $this->getInput('ftpPort', 'post'))
			->set('ftp.dir', $this->getInput('ftpDir', 'post'))
			->set('ftp.user', $this->getInput('ftpUser', 'post'))
			->set('ftp.pwd', $this->getInput('ftpPwd', 'post'))
			->set('ftp.timeout', abs(intval($this->getInput('ftpTimeout', 'post'))))
			->flush();
		$this->showMessage('ADMIN:success');
	}
}
?>