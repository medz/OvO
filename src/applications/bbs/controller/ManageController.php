<?php

Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
Wind::import('SRV:forum.srv.PwThreadManage');

/**
 * 帖子-管理操作
 *
 * @author peihong <jhqblxt@gmail.com> Dec 2, 2011
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: ManageController.php 24747 2013-02-20 03:13:43Z jieyin $
 * @package src.applications.bbs.controller
 */
class ManageController extends PwBaseController {
	
	public $action;
	
	protected $singleData = array();
	protected $manage;
	protected $doAction;
	protected $doCancel = array();
	
	/**
	 * preprocessing before any manage action
	 * 
	 * @see base/PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);

		if (!$this->loginUser->isExists()) {
			$this->showError('login.not');
		}
		$this->action = $handlerAdapter->getAction();
		$this->manage = $this->_getManage($this->action);
		if (($result = $this->manage->check()) !== true) {
			if (false === $result) $this->showError(new PwError('BBS:manage.permission.deny'));
			$this->showError($result->getError());
		}
	}
	
	/**
	 * manage action
	 */
	public function manageAction() {
		if (!$this->doAction) {
			$reason = Wekit::C()->site->get('managereasons', '');
			$this->setOutput(explode("\n", $reason), 'manageReason');
			$this->setOutput($this->action, 'action');
			$this->setTemplate('manage_threads');
		} else {
			if ($this->manage->user->getPermission('force_operate_reason')) {
				$reason = $this->getInput('reason', 'post');
				$reason or $this->showError('BBS:manage.error.empty.reason');
			}
			$sendnotice = $this->getInput('sendnotice', 'post');
			$this->manage->execute();
			if ($sendnotice) {
				$this->_sendMessage($this->action, $this->manage->getData());
			}
			$this->showMessage('operate.success');
		}
	}
	
	/**
	 * get manage handler
	 * 
	 * @param $action
	 */
	protected function _getManage($action) {
		$tids = $this->getInput('tids', 'post');
		$tid = $this->getInput('tid', 'post');
		if ($tids && !is_array($tids)) {
			$tids = explode(',', $tids);
		} elseif (!$tids && $tid) {
			$tids = array($tid);
		}
		$manage = new PwThreadManage(new PwFetchTopicByTid($tids), $this->loginUser);

		if (strpos($action,'do') === 0 && $action != 'down') {
			$this->doAction = true;
		} else {
			$this->doAction = false;

			$this->setOutput('帖子操作', 'title');
			$this->setOutput(count($manage->getData()), 'count');
			
			if (count($data = $manage->getData()) == 1) {
				$this->singleData = current($data);
			}
		}
		switch ($action) {
			case 'delete':
			case 'dodelete':
				$do = $this->_getDeleteManage($manage);
				break;
			case 'ban':
			case 'doban':
				$do = $this->_getBanManage($manage);
				break;
			case 'topped':
			case 'digest':
			case 'highlight':
			case 'up':
			case 'lock':
			case 'down':
			case 'docombined':
				$do = $this->_getCombinedManage($manage);
				break;
			case 'copy':
			case 'docopy':
				$do = $this->_getCopyManage($manage);
				break;
			case 'move':
			case 'domove':
				$do = $this->_getMoveManage($manage);
				break;
			case 'type':
			case 'dotype':
				$do = $this->_getTypeManage($manage);
				break;
			default:
				$do = null;
				break;
		}
		if (is_array($do)) {
			foreach ($do as $do1) {
				$manage->appendDo($do1);
			}
		} else {
			$manage->appendDo($do);
		}
		return $manage;
	}
	
	protected function _getCombinedManage($manage) {
		$do = array();
		if (!$this->doAction) {
			$operateThread = $manage->getPermission();
			$method = sprintf('_get%sManage', ucfirst($this->action));
			$do[] = $this->$method($manage);
			$others = $this->_getOtherActions($this->action);
			foreach ($others as $key => $value) {
				$method = sprintf('_get%sManage', ucfirst($value));
				$op = $this->$method($manage);
				if ($op->check($operateThread) !== true) {
					unset($operateThread[$value]);
				}
			}
			$this->setOutput($operateThread, 'operateThread');
			$this->setOutput('docombined', 'doaction');
		} else {
			$actions = $this->getInput('actions', 'post');
			foreach ((array)$actions as $key => $value) {
				$method = sprintf('_get%sManage', ucfirst($value));
				$do[] = $this->$method($manage);
			}
		}
		return $do;
	}

	protected function _getOtherActions($action) {
		$a1 = array('topped', 'digest', 'up', 'highlight');
		$a2 = array('lock', 'down');
		if (in_array($action, $a1)) {
			return array_diff($a1, array($action));
		}
		if (in_array($action, $a2)) {
			return array_diff($a2, array($action));
		}
		return array();
	}

	protected function _getDigestManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoDigest');
		$do = new PwThreadManageDoDigest($manage);
		if (!$this->doAction) {

		} else {
			$digest = $this->getInput('digest', 'post');
			$do->setDigest($digest);
			$do->setReason($this->getInput('reason', 'post'));
			!$digest && $this->doCancel[] = 'dodigest';
		}
		return $do;
	}

	protected function _getToppedManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoTopped');
		$do = new PwThreadManageDoTopped($manage);
		if (!$this->doAction) {
			$selectedFids = array();
			if ($this->singleData) {
				$defaultTopped = $this->singleData['topped'];
				$sort = Wekit::load('forum.PwSpecialSort')->getSpecialSortByTid($this->singleData['tid']);
				$defaultTopped == 3 && $selectedFids = array_keys($sort);
				$current = current($sort);
				$toppedOvertime = $current['end_time'] ? Pw::time2str($current['end_time'], 'Y-m-d') : '';
			} else {
				$defaultTopped = 1;
				$toppedOvertime = '';
			}
			$operateThread = $manage->getPermission();
			$forumOption = $operateThread['topped_type'] > 2 ? Wekit::load('forum.srv.PwForumService')->getForumOption($selectedFids) : '';
			$this->setOutput($toppedOvertime, 'toppedOvertime');
			$this->setOutput($defaultTopped, 'defaultTopped');
			$this->setOutput($forumOption, 'forumOption');
		} else {
			list($topped, $toppedOvertime, $toppedFids) = $this->getInput(array('topped', 'topped_overtime', 'topped_fids'), 'post');
			$do->setTopped($topped);
			$do->setFids($toppedFids);
			$do->setOvertime($toppedOvertime);
			$do->setReason($this->getInput('reason', 'post'));
			!$topped && $this->doCancel[] = 'dotopped';
		}
		return $do;
	}
	
	protected function _getUpManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoUp');
		$do = new PwThreadManageDoUp($manage);
		if (!$this->doAction) {

		} else {
			$uptime = $this->getInput('uptime', 'post');
			$do->setUptime($uptime);
			$do->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}

	protected function _getHighlightManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoHighlight');
		$do = new PwThreadManageDoHighlight($manage);
		if (!$this->doAction) {
			if ($this->singleData) {
				Wind::import("LIB:utility.PwHighlight");
				$hightlight = new PwHighlight();
				$hightlightStyle = $hightlight->parseHighlight($this->singleData['highlight']);
				$overtime = Wekit::load('forum.PwOvertime')->getOvertimeByTidAndType($this->singleData['tid'], 'highlight');
				$hightlightOvertime = ($overtime && $overtime['overtime']) ? Pw::time2str($overtime['overtime'], 'Y-m-d') : '';
			} else {
				$hightlightStyle = array('color' => '#F50');
				$hightlightOvertime = '';
			}
			$this->setOutput($hightlightStyle, 'hightlightStyle');
			$this->setOutput($hightlightOvertime, 'hightlightOvertime');
		} else {
			list($bold, $italic, $underline, $color, $highlightOvertime) = $this->getInput(array('bold', 'italic', 'underline', 'color', 'highlight_overtime'), 'post');
			Wind::import("LIB:utility.PwHighlight");
			$hightlight = new PwHighlight();
			$hightlight->setColor($color);
			$hightlight->setBold($bold);
			$hightlight->setItalic($italic);
			$hightlight->setUnderline($underline);
			$do->setHighlight($hightlight->getHighlight());
			$do->setOvertime($highlightOvertime);
			$do->setReason($this->getInput('reason', 'post'));
			if (!$color && !$bold && !$italic && !$underline) $this->doCancel[] = 'dohighlight';
		}
		return $do;
	}

	protected function _getDeleteManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoDeleteTopic');
		$do = new PwThreadManageDoDeleteTopic($manage);
		if (!$this->doAction) {
			$this->setOutput('dodelete', 'doaction');
		} else {
			$deductCredit = $this->getInput('deductCredit', 'post');
			$do->setIsDeductCredit($deductCredit)
				->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}
	
	protected function _getDownManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoDown');
		$do = new PwThreadManageDoDown($manage);
		if (!$this->doAction) {
			
		} else {
			list($downtime, $downed) = $this->getInput(array('downtime', 'downed'), 'post');
			$do->setDowntime($downtime)->setDowned($downed)->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}
	
	protected function _getLockManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoLock');
		$do = new PwThreadManageDoLock($manage);
		if (!$this->doAction) {
			if ($this->singleData) {
				$defaultLocked = Pw::getstatus($this->singleData['tpcstatus'], PwThread::STATUS_CLOSED) ? 2 : 1;
			} else {
				$defaultLocked = 1;
			}
			$this->setOutput($defaultLocked, 'defaultLocked');
		} else {
			$locked = $this->getInput('locked', 'post');
			$do->setLocked($locked)->setReason($this->getInput('reason', 'post'));
			!$locked && $this->doCancel[] = 'dolock';
		}
		return $do;
	}

	protected function _getMoveManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoMove');
		$do = new PwThreadManageDoMove($manage);
		if (!$this->doAction) {
			$this->setOutput($this->_getFroumService()->getForumOption($do->fid), 'option_html');
			$this->setOutput('domove', 'doaction');
		} else {
			list($fid, $topictype) = $this->getInput(array('fid', 'topictype'), 'post');
			$do->setFid($fid)->setTopictype($topictype)->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}

	protected function _getTypeManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoType');
		$do = new PwThreadManageDoType($manage);
		if (!$this->doAction) {
			$topicTypes = $do->getTopicTypes();
			$this->setOutput($topicTypes,'topicTypes');
			$this->setOutput('dotype', 'doaction');
		} else {
			list($topicType, $subTopicType) = $this->getInput(array('topictype', 'sub_topictype'), 'post');
			$do->setTopictype($topicType, $subTopicType);
			$do->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}

	protected function _getCopyManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoCopy');
		$do = new PwThreadManageDoCopy($manage);
		
		if (!$this->doAction) {
			$this->setOutput('docopy', 'doaction');
			$this->setOutput($this->_getFroumService()->getForumOption($do->fid), 'option_html');
		} else {
			list($fid, $topictype) = $this->getInput(array('fid', 'topictype'), 'post');
			$do->setFid($fid)->setTopictype($topictype)->setReason($this->getInput('reason', 'post'));
		}
		return $do;
	}

	/**
	 * ban manage
	 * 
	 * @return PwThreadManageDoBan
	 */
	protected function _getBanManage($manage) {
		Wind::import('SRV:forum.srv.manage.PwThreadManageDoBan');
		$do = new PwThreadManageDoBan($manage, $this->loginUser);
		if ($this->doAction) {
			$banInfo = new stdClass();
			$banInfo->types = $this->getInput('types', 'post');
			$banInfo->end_time = $this->getInput('end_time', 'post');
			$banInfo->reason = $this->getInput('reason', 'post');
			$banInfo->ban_range = intval($this->getInput('ban_range', 'post'));
			$banInfo->sendNotice = intval($this->getInput('sendnotice', 'post'));
			$do->setBanInfo($banInfo)->setBanUids($this->getInput('uids', 'post'))->setDeletes($this->getInput('delete', 'post'));
		} else {
			/* @var $banService PwUserBanService */
			$banService = Wekit::load('user.srv.PwUserBanService');
			$uid = $this->getInput('uid', 'get');
			if ($uid) {
				$do->setBanUids($uid);
			}
			$info = $do->getBanUsers();
			$this->setOutput($banService->getBanType(), 'types');
			$this->setOutput($info, 'userNames');
			$this->setOutput(count($info), 'count');
			$this->setOutput($do->getRight(), 'right');
			$this->setOutput('doban', 'doaction');
			$this->setOutput('用户禁止', 'title');
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
		if (!is_array($threads) || !$threads || !$action || $action == 'doban') return false;
		$noticeService = Wekit::load('message.srv.PwNoticeService');
		$reason = $this->getInput('reason', 'post');
		foreach ($threads as $thread) {
			$params = array();
			$params['manageUsername'] = $this->manage->user->username;
			$params['manageUserid'] = $this->manage->user->uid;
			$params['manageThreadTitle'] = $thread['subject'];
			$params['manageThreadId'] = $thread['tid'];
			//$this->params['_other']['reason'] && $params['manageReason'] = $this->params['_other']['reason'];
			$reason && $params['manageReason'] = $reason;
			if ($action == 'docombined') {
				$actions = $this->getInput('actions', 'post');
				$tmp = array();
				foreach ($actions as $v){
					$tmp[] = $this->_getManageActionName('do' . $v);
				}
				$tmp && $params['manageTypeString'] = implode(',', $tmp);
			} else {
				$params['manageTypeString'] = $this->_getManageActionName($action);
			}
			$noticeService->sendNotice($thread['created_userid'], 'threadmanage', $thread['tid'], $params);
		}
	}
	
	protected function _getManageActionName($action) {
		$resource = Wind::getComponent('i18n');
		$message = $resource->getMessage("BBS:manage.operate.name.$action");
		if (in_array($action, $this->doCancel)) {
			$message = $resource->getMessage("BBS:manage.operate.action.cancel") . $message;
		}
		return $message;
	}
	
	protected function _getFroumService() {
		return Wekit::load('forum.srv.PwForumService');
	}
}
