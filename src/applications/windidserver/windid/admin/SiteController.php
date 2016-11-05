<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('APPS:windid.admin.WindidBaseController');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: SiteController.php 24709 2013-02-16 07:36:55Z jieyin $ 
 * @package 
 */
class SiteController extends WindidBaseController {
	
	public function run() {
		$config = Wekit::C()->getValues('site');
		$this->setOutput($config, 'config');
	}
	
	public function dorunAction() {
		$config = new PwConfigSet('site');
		$config->set('info.name', $this->getInput('infoName', 'post'))
			->set('info.url', $this->getInput('infoUrl', 'post'))
			->set('time.timezone', intval($this->getInput('timeTimezone', 'post')))
			->set('time.cv', intval($this->getInput('timecv', 'post')))
			->set('debug', $this->getInput('debug', 'post'))
			->set('cookie.path', $this->getInput('cookiePath'), 'post')
			->set('cookie.domain', $this->getInput('cookieDomain', 'post'))
			->set('cookie.pre', $this->getInput('cookiePre', 'pre'))
			->flush();
		$this->showMessage('ADMIN:success');
	}
}
?>