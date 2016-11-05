<?php
Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 * @author $Author: jieyin $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: CreditController.php 24718 2013-02-17 06:42:06Z jieyin $ 
 * @package 
 */
class CreditController extends WindidBaseController { 

	public function run() {
		$config = Wekit::C()->getValues('credit');
		$this->setOutput($config['credits'], 'credits');	
	}
	
	public function docreditAction() {
		$credits = $this->getInput('credits', 'post');
		$newcredits = $this->getInput('newcredits', 'post');
		Wind::import('WSRV:config.srv.WindidCreditSetService');
		$srv = new WindidCreditSetService();
		$srv->setCredits($credits, $newcredits);

		$srv2 = Wekit::load('WSRV:notify.srv.WindidNotifyService');
		$srv2->send('setCredits', array());
		$this->showMessage('WINDID:success');
	}
	
	public function doDeletecreditAction() {
		$creditId = (int) $this->getInput("creditId");
		if ($creditId < 5) $this->showError('WINDID:fail');
		Wind::import('WSRV:config.srv.WindidCreditSetService');
		
		$srv = new WindidCreditSetService();
		if ((!$srv->deleteCredit($creditId))) {
			$this->showError('WINDID:fail');
		}
		$srv2 = Wekit::load('WSRV:notify.srv.WindidNotifyService');
		$srv2->send('setCredits', array());
		$this->showMessage('WINDID:success');
	}
}
?>