<?php
Wind::import('SRV:task.srv.PwTaskComplete');
Wind::import('SRV:task.srv.base.PwTaskCompleteInterface');

/**
 * 求粉丝的任务完成条件添加
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.service.task.srv.condition
 */
class PwTaskMemberFansDo implements PwTaskCompleteInterface {
	
	/**
	 * 加关注的完成条件
	 *
	 * @param int $uid 用户A
	 * @param int $toUid  被关注的用户B
	 * @return 
	 */
	public function addFollow($uid, $toUid) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		$bp = new PwTaskComplete($toUid, $this);
		return $bp->doTask('member', 'fans');
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskCompleteInterface::doTask()
	 */
	public function doTask($conditions, $step) {
		$isComplete = false;
		if (isset($step['percent']) && $step['percent'] == '100%') {
			$isComplete = true;
		} else {
			$step['num'] = isset($step['num']) ? intval($step['num'] + 1) : 1;
			$step['percent'] = intval(($step['num'] / $conditions['num']) * 100) . '%';
			($step['num'] == $conditions['num']) && $isComplete = true;
		}
		return array('isComplete' => $isComplete, 'step' => $step);
	}
}