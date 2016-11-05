<?php
Wind::import('SRV:task.dm.PwTaskUserDm');
Wind::import('SRV:task.srv.PwTaskApply');
/**
 * 获取奖励的BP
 * 
 * 1：如果该任务是自动发放的任务，自动任务完成之后更新pw_task_cache"表：
 * 1-1：如果该任务是周期任务：更新task_ids中完成的自动周期任务的ID列表
 * 1-2：该任务是否是某些自动任务的前置任务，则将满足如下条件的任务推送给用户acceptTask:
 * 1-2-1：已该任务为前置的任务
 * 1-2-2：这些任务是自动任务
 * 1-2-3：这些任务是没有过期的任务
 * 1-2-4：这些任务是当前用户所在组可以申领的任务
 * 
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskGainReward.php 23446 2013-01-09 11:59:46Z xiaoxia.xuxx $
 * @package src.service.task.srv
 */
class PwTaskGainReward extends PwBaseHookService {
	
	private $uid = 0;
	private $taskId = 0;
	private $myTask = array();
	
	private $error = null;
	
	public $type = '';
	public $taskInfo = array();

	/**
	 * 任务
	 *
	 * @param int $uid
	 * @param int $task_id
	 */
	public function __construct($uid, $task_id) {
		$this->uid = intval($uid);
		$this->taskId = intval($task_id);
		$this->error = $this->init();
		if (true !== $this->error) return;
		parent::__construct('task_gainreward', $this);
	}

	/**
	 * 初始化
	 *
	 * @return PwError|boolean
	 */
	private function init() {
		if (!$this->uid) return new PwError('TASK:request.illegal');
		$task = $this->_getTaskDs()->get($this->taskId);
		if (!$task) return new PwError('TASK:id.illegal');
		$canContinue = $this->allowContinue($task);
		if (true !== $canContinue) return $canContinue;
		$task['reward'] = unserialize($task['reward']);
		$task['conditions'] = unserialize($task['conditions']);
		$this->taskInfo = $task;
		$this->type = $this->taskInfo['reward']['type'];
		return true;
	}

	/**
	 * 获取奖励
	 * 
	 * 1：如果该任务是自动发放的任务，自动任务完成之后更新pw_task_cache"表：
	 * 1-1：如果该任务是周期任务：更新task_ids中完成的自动周期任务的ID列表
	 * 1-2：该任务是否是某些自动任务的前置任务，则将满足如下条件的任务推送给用户acceptTask:
	 * 1-2-1：已该任务为前置的任务
	 * 1-2-2：这些任务是自动任务
	 * 1-2-3：这些任务是没有过期的任务
	 * 1-2-4：这些任务是当前用户所在组可以申领的任务
	 * 
	 * @param int $task_id 任务ID
	 * @return boolean|PwError
	 */
	public function gainReward() {
		if (true !== $this->error) return $this->error;
		$dm = new PwTaskUserDm();
		$dm->setTaskStatus(PwTaskUser::COMPLETE)->setFinishTime(Pw::getTime());
		$this->_getTaskUserDs()->update($this->taskId, $this->uid, $dm);
		$this->updateUserCache();
		$r = $this->runWithVerified('gainReward', $this->uid, $this->taskInfo['reward'], $this->taskInfo['title']);
		if (true !== $r) return $r;
		return $this->sendNextAutoApplicableTaskList();
	}

	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return 'PwTaskRewardDoBase';
	}

	/**
	 * 1：如果该任务是自动发放的任务，自动任务完成之后更新pw_task_cache"表：
	 * 1-1：如果该任务是周期任务：更新task_ids中完成的自动周期任务的ID列表
	 * array(0 => id, 1 => array());
	 * @return boolean
	 */
	private function updateUserCache() {
		if (!$this->taskInfo['period']) return true;
		$userCache = $this->_getTaskDs()->getTaskCacheByUid($this->uid);
		if (!$userCache) $userCache = array('', array());
		array_push($userCache[1], $this->taskId);
		array_unique($userCache[1]);
		return $this->_getTaskDs()->updateUserTaskCache($this->uid, $userCache);
	}

	/**
	 * 将该任务的后置自动任务发送给用户
	 *
	 * @param int $task_id
	 * @return boolean
	 */
	private function sendNextAutoApplicableTaskList() {
		/* @var $taskService PwTaskService */
		$taskService = Wekit::load('task.srv.PwTaskService');
		$childs = $taskService->getNextAutoApplicableTaskList($this->taskId, $this->uid);
		if (!$childs) return true;
		$userService = new PwTaskApply($this->uid);
		$userService->autoApplies($childs);
		return true;
	}

	/**
	 * 判断一个任务状态是否可以继续申领奖励
	 *
	 * @param array $task
	 * @return boolean|PwError
	 */
	private function allowContinue($task) {
		$time = Pw::getTime();
		if ($task['end_time'] && ($task['end_time'] < $time)) {
			return new PwError('TASK:overtime');
		}
		if (0 == $task['is_open']) {
			return new PwError('TASK:close');
		}
		if ($task['start_time'] && ($task['start_time'] > $time)) {
			return new PwError('TASK:no.open');
		}
		$userTaskStatu = $this->_getTaskUserDs()->get($this->uid, $this->taskId);
		if (!$userTaskStatu) return false;
		if (4 == $userTaskStatu['task_status']) return new PwError('TASK:already.gain.reward');
		if (2 != $userTaskStatu['task_status']) return new PwError('TASK:task.no.complete');
		$userTaskStatu['step'] = unserialize($userTaskStatu['step']);
		$this->myTask = $userTaskStatu;
		return true;
	}

	/**
	 * 获得任务DS
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