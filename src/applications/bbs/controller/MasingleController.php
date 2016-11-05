<?php

Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByTidAndPids');
Wind::import('SRV:forum.srv.PwThreadManage');

/**
 * @author peihong <jhqblxt@gmail.com> Dec 2, 2011
 * @link
 * @copyright
 * @license
 */

class MasingleController extends PwBaseController {
	
	public $action;

	protected $manage;
	protected $doAction;
	protected $_doCancel = array();

	protected $_hasThread = false;
	protected $_jumpurl = '';

	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->showError('login.not');
		}
		$this->action = $handlerAdapter->getAction();
		$this->manage = $this->_getManage($this->action);
		if (($result = $this->manage->check()) !== true) {
			$this->showError($result->getError());
		}
	}

	public function manageAction() {
		if (!$this->doAction) {
			$reason = Wekit::C()->site->get('managereasons', '');
			$this->setOutput(explode("\n", $reason), 'manageReason');
			$this->setOutput($this->action, 'action');
			$this->setOutput(count($this->manage->data), 'count');
			$this->setTemplate('masingle_threads');
		} else {
			$sendnotice = $this->getInput('sendnotice', 'post');
			$this->manage->execute();
			if ($sendnotice) {
				$this->_sendMessage($this->action, $this->manage->getData());
			}
			if ($this->action == 'dodelete' && $this->_hasThread) {
				$this->_jumpurl = 'bbs/thread/run?fid=' . current($this->manage->getFids());
			}
			$this->showMessage('operate.success', $this->_jumpurl);
		}
	}

	protected function _getManage($action) {
		$pids = $this->getInput('pids', 'post');
		$tid = $this->getInput('tid', 'post');
		$pid = $this->getInput('pid', 'post');
		if ($pids && !is_array($pids)) {
			$pids = explode(',', $pids);
		} elseif (!$pids && $pid) {
			$pids = array($pid);
		}
		if (!$pids) {
			$this->showError('operate.select');
		}
		in_array('0', $pids) && $this->_hasThread = true;
		$manage = new PwThreadManage(new PwFetchReplyByTidAndPids($tid, $pids), $this->loginUser);
		
		if (strpos($action, 'do') === 0) {
			$getMethod = sprintf('_get%sManage', ucfirst(substr($action, 2)));
			$this->doAction = true;
		} else {
			$getMethod = sprintf('_get%sManage', ucfirst($action));
			$this->doAction = false;
		}
		if (method_exists($this, $getMethod)) {
			$do = $this->$getMethod($manage);
			$manage->appendDo($do);
		}
		return $manage;
	}

	protected function _getDeleteManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoDeleteReply');
		$do = new PwThreadManageDoDeleteReply($manage);
		if (!$this->doAction) {
			$this->setOutput('dodelete', 'doaction');
		} else {
			$deductCredit = $this->getInput('deductCredit', 'post');
			$reason = $this->getInput('reason', 'post');
			$do->setIsDeductCredit($deductCredit)
				->setReason($reason);
		}
		return $do;
	}

	/**
	 * 已阅操作
	 *
	 * @param obj $manage
	 * @return PwThreadManageDoInspect
	 */
	protected function _getInspectManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoInspect');
		$do = new PwThreadManageDoInspect($manage);
		if (!$this->doAction) {
			$this->showError('data.error');
		} else {
			$lou = $this->getInput('lou');
			$do->setLou($lou);
		}
		return $do;
	}

	/**
	 * 屏蔽操作
	 *
	 * @param obj $manage
	 * @return PwThreadManageDoInspect
	 */
	protected function _getShieldManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoShield');
		$do = new PwThreadManageDoShield($manage);
		if (!$this->doAction) {
			$this->setOutput('doshield', 'doaction');
			$this->setOutput($manage->data[0]['ifshield'], 'defaultShield');
		} else {
			list($reason,$ifShield) = $this->getInput(array('reason','ifShield'), 'post');
			$do->setReason($reason)->setIfShield($ifShield);
			!$ifShield && $this->_doCancel[] = 'doshield';
		}
		return $do;
	}

	/**
	 * 提醒操作
	 *
	 * @param obj $manage
	 * @return PwThreadManageDoInspect
	 */
	protected function _getRemindManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoRemind');
		$do = new PwThreadManageDoRemind($manage);
		if (!$this->doAction) {
			$this->setOutput('doremind', 'doaction');
			$this->setOutput($manage->data[0]['manage_remind'], 'defaultRemind');
		} else {
			list($reason,$ifRemind) = $this->getInput(array('reason','ifRemind'), 'post');
			$do->setReason($reason)->setIfRemind($ifRemind);
			!$ifRemind && $this->_doCancel[] = 'doremind';
		}
		return $do;
	}

	/**
	 * 帖内置顶操作
	 *
	 * @param obj $manage
	 * @return PwThreadManageDoInspect
	 */
	protected function _getToppedReplyManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoToppedReply');
		$do = new PwThreadManageDoToppedReply($manage);
		if (!$this->doAction) {
			$this->showError('data.error');
		} else {
			list($lou,$topped) = $this->getInput(array('lou','topped'));
			$do->setLou($lou)->setTopped($topped);
		}
		return $do;
	}
	
	/* (non-PHPdoc)
	 * @see WindController::resolvedActionMethod()
	 */
	public function resolvedActionMethod($handlerAdapter) {
		return $this->resolvedActionName('manage');
	}

	/**
	 * send messages
	 */
	protected function _sendMessage($action, $threads) {
		if (!is_array($threads) || !$threads || !$action) return false;
		$noticeService = Wekit::load('message.srv.PwNoticeService');
		$reason = $this->getInput('reason');
		foreach ($threads as $thread) {
			$params = array();
			$params['manageUsername'] = $this->manage->user->username;
			$params['manageUserid'] = $this->manage->user->uid;
			$params['manageThreadTitle'] = $thread['subject'];
			$params['manageThreadId'] = $thread['tid'];
			$params['manageTypeString'] = $this->_getManageActionName($action);
			$reason && $params['manageReason'] = $reason;
			$noticeService->sendNotice($thread['created_userid'], 'threadmanage', $thread['tid'], $params);
		}
		return true;
	}
	
	protected function _getManageActionName($action) {
		$resource = Wind::getComponent('i18n');
		$message = $resource->getMessage("BBS:manage.operate.name.$action");
		if (in_array($action, $this->_doCancel)) {
			$message = $resource->getMessage("BBS:manage.operate.action.cancel") . $message;
		}
		return $message;
	}	
}