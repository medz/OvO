<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');

/**
 * 帖子管理操作-已阅
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoDeleteReply.php 14354 2012-07-19 10:36:06Z jieyin $
 * @package forum
 */

class PwThreadManageDoInspect extends PwThreadManageDo {
	
	protected $lou;
	protected $tid;
	protected $username;

	public function __construct(PwThreadManage $srv){
		parent::__construct($srv);
		$this->username = $srv->user->username;
	}
	
	public function check($permission) {
		return (isset($permission['read']) && $permission['read']) ? true : false;
	}

	public function setLou($lou) {
		$this->lou = $lou;
		return $this;
	}
	
	public function gleanData($value) {
		$this->tid = $value['tid'];
	}
	
	public function run() {
		$thread = $this->_getThreadDs()->getThread($this->tid);
		list($lou) = explode("\t",$thread['inspect']);
		if (($this->lou > intval($lou) && $this->lou <= $thread['replies']) || !$thread['inspect']) {
			$inspect = $this->lou."\t".$this->username;
			Wind::import('SRV:forum.dm.PwTopicDm');
			$topicDm = new PwTopicDm($thread['tid']);
			$topicDm->setInspect($inspect);
			$this->_getThreadDs()->updateThread($topicDm,PwThread::FETCH_MAIN);
			//管理日志入口
			Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'readed', $this->srv->getData(), $this->_reason, $this->lou, true);
		}
	}
	
	/**
	 * Enter description here ...
	 *
	 * @return PwThread
	 */
	public function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
	
}