<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: OpenBaseController.php 28968 2013-05-31 12:05:48Z gao.wanggao $ 
 * @package 
 */
class OpenBaseController extends PwBaseController {
	
	public $app = array();
	public $appid = 0;
	
	public  function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$charset = 'utf-8';
		$_windidkey = $this->getInput('windidkey', 'get');
		$_time = (int)$this->getInput('time', 'get');
		$_clientid = (int)$this->getInput('clientid', 'get');
		if (!$_time || !$_clientid) $this->output(WindidError::FAIL);
		$clent = $this->_getAppDs()->getApp($_clientid);
		if (!$clent) $this->output(WindidError::FAIL);
		if (WindidUtility::appKey($clent['id'], $_time, $clent['secretkey'], $this->getRequest()->getGet(null), $this->getRequest()->getPost()) != $_windidkey)  $this->output(WindidError::FAIL);
		
		$time = Pw::getTime();
		if ($time - $_time > 1200) $this->output(WindidError::TIMEOUT);
		$this->appid = $_clientid;
	}
	
	protected function setDefaultTemplateName($handlerAdapter) {
		$this->setTemplate('');
	}
	
	public function run() {
		$this->output(0);
	}
	
	protected function output($message = '') {
		if (is_numeric($message)) {
			echo $message;
			exit;
		} else {
			header('Content-type: application/json; charset=' . Wekit::V('charset'));
			echo Pw::jsonEncode($message);
			exit;
		}
	}
	
	private function _getAppDs() {
		return Wekit::load('WSRV:app.WindidApp');
	}
}
?>