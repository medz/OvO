<?php
Wind::import('SRV:message.srv.do.PwMessageDoBase');
Wind::import('SRV:task.srv.PwTaskComplete');
Wind::import('SRV:task.srv.base.PwTaskCompleteInterface');

/**
 * 用户发消息的任务扩展
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskMemberMsgDo.php 18618 2012-09-24 09:31:00Z jieyin $
 * @package service.task.srv.condition
 */
class PwTaskMemberMsgDo extends PwMessageDoBase implements PwTaskCompleteInterface { 
	
	private $name;
	
	/* (non-PHPdoc)
	 * @see PwMessageDoBase::addMessage()
	 */
	public function addMessage($uid, $fromUid, $content) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		/* @var $dm PwMessageMessagesDm */
		$bo = Wekit::getLoginUser();
		if ($fromUid != $bo->uid) return false;
		$user = Wekit::load('user.PwUser')->getUserByUid($uid);
		if (!$user) return false;
		$this->name = $user['username'];
		$bp = new PwTaskComplete($bo->uid, $this);
		$bp->doTask('member', 'msg');
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskCompleteInterface::doTask()
	 */
	public function doTask($conditions, $step) {
		if ($conditions['name'] && $conditions['name'] != $this->name) return false;
		$step['percent'] = '100%';
		return array('isComplete' => true, 'step' => $step);
	}
}
?>