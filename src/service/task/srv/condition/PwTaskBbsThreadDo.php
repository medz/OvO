<?php
Wind::import('SRV:forum.srv.post.do.PwPostDoBase');
Wind::import('SRV:task.srv.PwTaskComplete');
Wind::import('SRV:task.srv.base.PwTaskCompleteInterface');

/**
 * 发帖时候的任务扩展
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskBbsThreadDo.php 20491 2012-10-30 08:15:09Z xiaoxia.xuxx $
 * @package service.task.srv.condition
 */
class PwTaskBbsThreadDo extends PwPostDoBase implements PwTaskCompleteInterface {
	
	private $tid;
	private $fid;
	private $uid;

	public function __construct(PwPost $pwpost) {
		$this->uid = $pwpost->user->uid;
	}
	
	/* (non-PHPdoc)
	 * @see PwPostDoBase::addThread()
	 */
	public function addThread($tid) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		$this->tid = $tid;
		$thread = Wekit::load('forum.PwThread')->getThread($tid);
		$this->fid = $thread['fid'];
		$bp = new PwTaskComplete($this->uid, $this);
		$bp->doTask('bbs', 'postThread');
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskCompleteInterface::doTask()
	 */
	public function doTask($conditions, $step) {
		if ($conditions['fid'] != $this->fid) return false;
		$isComplete = false;
		if (isset($step['percent']) && $step['percent'] == '100%') {
			$isComplete = true;
		} else {
			$step['current'] = isset($step['current']) ? intval($step['current'] + 1) : 1;
			$step['percent'] = intval($step['current'] / $conditions['num'] * 100) . '%';
			$step['percent'] == '100%' && $isComplete = true;
		}
		return array('isComplete' => $isComplete, 'step' => $step);
	}
}
?>