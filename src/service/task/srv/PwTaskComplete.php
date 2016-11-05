<?php
Wind::import('SRV:task.dm.PwTaskUserDm');
/**
 * 完成任务的BP
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskComplete.php 9458 2012-05-07 07:56:42Z xiaoxia.xuxx $
 * @package src.service.task.srv
 */
class PwTaskComplete {
	private $num = 100;
	private $uid = 0;
	/* @var $doTask PwTaskCompleteInterface */
	private $doTask = null;

	/**
	 * 构造函数
	 *
	 * @param int $uid
	 * @param PwTaskCompleteInterface $doTask
	 */
	public function __construct($uid, PwTaskCompleteInterface $doTask) {
		$this->uid = intval($uid);
		$this->doTask = $doTask;
	}

	/**
	 * 做任务
	 * 
	 * 1、查询当前用户是否有正在进行的任务，没有则不执行
	 * 2、初始化
	 * 3、获得用户进行的任务详细信息
	 * 4、过滤任务中已经过期和关闭的任务，这些任务都不能再被继续完成
	 * 5、执行任务判断：逐个判断注册过来的任务-将任务的条件和已经完成的step传递给任务：任务判断condition和step，如果step还没有完成则更新该step
	 * 
	 * @param string $type 任务类别  后台设置中的“会员”/"论坛“类，
	 * @param string $child 任务类别中的小类， 比如”会员“->'求粉丝'
	 * @return boolean
	 */
	public function doTask($type, $child) {
		if (!$this->doTask) return false;
		$myTasks = $this->_getTaskUserDs()->getMyTaskByStatus($this->uid, 1, $this->num, 0);
		if (!$myTasks) return true;
		$taskList = $this->_getTaskDs()->gets(array_keys($myTasks));
		if (!$taskList) return true;
		$time = Pw::getTime();
		foreach ($taskList as $id => $task) {
			if (($task['end_time'] && ($task['end_time'] < $time)) || ($task['is_open'] == 0)) {
				continue;
			}
			$conditions = unserialize($task['conditions']);
			if ($conditions['type'] != $type || $conditions['child'] != $child) continue;
			$myStatus = $myTasks[$id];
			$step = unserialize($myStatus['step']);
			if (!is_array($step)) $step = $myStatus['step'];
			$this->_doTask($task, $conditions, $step);
		}
		return true;
	}

	/**
	 * 更新用户任务
	 * 
	 * @param int $taskId
	 * @param array $conditions
	 * @param array $step
	 * @return boolean
	 */
	private function _doTask($taskInfo, $conditions, $step) {
		$result = $this->doTask->doTask($conditions, $step);
		if (!is_array($result) || !isset($result['isComplete']) || !isset($result['step'])) return false;
		$dm = new PwTaskUserDm();
		$dm->setTaskStatus($result['isComplete'] === true ? 2 : 1);
		if (true === $result['isComplete']) {
			/* @var $notice PwNoticeService */
			$notice = Wekit::load('message.srv.PwNoticeService');
			$taskInfo['complete'] = 1;
			$notice->sendNotice($this->uid, 'task', $this->uid, $taskInfo);
			is_array($result['step']) && $result['step']['percent'] = '100%';
		}
		$dm->setStep(is_array($result['step']) ? serialize($result['step']) : $result['step']);
		return $this->_getTaskUserDs()->update($taskInfo['taskid'], $this->uid, $dm);
	}

	/**
	 * 获得 任务的Ds
	 *
	 * @return PwTask
	 */
	private function _getTaskDs() {
		return Wekit::load('task.PwTask');
	}

	/**
	 * @return PwTaskUser
	 */
	private function _getTaskUserDs() {
		return Wekit::load('task.PwTaskUser');
	}
}