<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');

/**
 * 帖内管理操作 - 提醒
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwThreadManageDoRemind extends PwThreadManageDo {
	
	protected $tids;
	protected $pids;
	protected $ifRemind;
	protected $username;
	
	public function __construct(PwThreadManage $srv){
		$this->username = $srv->user->username;
	}
	
	public function check($permission) {
		return (isset($permission['remind']) && $permission['remind']) ? true : false;
	}

	public function gleanData($value) {
		if ($value['pid']) {
			$this->pids[] = $value['pid'];
		} else {
			$this->tids[] = $value['tid'];
		}
	}
	
	public function run() {
		$remind = $this->ifRemind ? $this->_buildRemind() : '';
		if ($this->pids) {
			Wind::import('SRV:forum.dm.PwReplyDm');
			$topicDm = new PwReplyDm(true);
			$topicDm->setManageRemind($remind);
			$this->_getThreadDs()->batchUpdatePost($this->pids, $topicDm);
		}
		if ($this->tids) {
			Wind::import('SRV:forum.dm.PwTopicDm');
			$topicDm = new PwTopicDm(true);
			$topicDm->setManageRemind($remind);
			$this->_getThreadDs()->batchUpdateThread($this->tids, $topicDm, PwThread::FETCH_CONTENT);
		}
	}

	public function setIfRemind($ifRemind) {
		$this->ifRemind = intval($ifRemind);
		return $this;
	}
	
	/**
	 * Enter description here ...
	 *
	 * @return PwThread
	 */
	public function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
	
	/**
	 * 格式化提醒
	 * 
	 * @param string $reason
	 * @return string reason,username,time
	 */
	protected function _buildRemind() {
		return $this->reason . ',' . $this->username . ',' . Pw::getTime();
	}
}