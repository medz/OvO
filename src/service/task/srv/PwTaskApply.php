<?php
Wind::import('SRV:task.dm.PwTaskUserDm');
/**
 * 任务获取BP
 * 
 * 申领任务：
 * 1、判断该任务是否能够被当前用户申领：
 * 1-1：是否已过期
 * 1-2：该任务是否已经启用
 * 1-3：该用户是否已申领该任务（该任务非周期任务）
 * 1-4：该任务为周期任务，该用户已完成该任务，但是该任务周期是否已达到下次申领时间。
 * 1-5：该任务如果有前置任务，该前置任务是否已经被完成。
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskApply.php 11641 2012-06-11 09:26:41Z xiaoxia.xuxx $
 * @package src.service.task.srv
 */
class PwTaskApply {
	private $uid = null;
	private $gids = array();

	/**
	 * 申请初始化
	 *
	 * @param int $uid 用户ID
	 */
	public function __construct($uid) {
		$this->uid = $uid;
		/* @var $userService PwUserService */
		$userService = Wekit::load('user.srv.PwUserService');
		$this->gids = $userService->getGidsByUid($uid);
		/*gid等于-1则该任务对所有用户组有效*/
		$this->gids[] = -1;
	}

	/**
	 * 申请任务入口
	 *
	 * @param int $taskId 任务ID
	 * @return true|PwError
	 */
	public function apply($taskId) {
		if (null === $this->uid) return new PwError('TASK:request.illegal');
		if (1 > $taskId = intval($taskId)) return new PwError('TASK:id.illegal');
		$task = $this->_getTaskDs()->get($taskId);
		if (!$task) return new PwError('TASK:id.illegal');
		$canApplied = $this->allowTaskApplied($task);
		return (true !== $canApplied) ? $canApplied : $this->doApply($task);
	}

	/**
	 * 自动接受任务接口
	 *
	 * 自动接受之后，将接受成功的一个ID更新到task_cache表
	 * 1：如果该任务是自动发放的任务，自动任务完成之后更新pw_task_cache"表：
	 * 1-1：如果该任务是周期任务：更新task_ids中完成的自动周期任务的ID列表
	 * 
	 * @param array $taskIds
	 * @return boolean
	 */
	public function autoApplies($taskIds) {
		if (null === $this->uid) return false;
		$taskList = $this->_getTaskDs()->gets($taskIds);
		if (!$taskList) return new PwError('TASK:id.illegal');
		$cacheId = 0;
		$periodCache = array();
		/* @var $notice PwNoticeService */
		$notice = Wekit::load('message.srv.PwNoticeService');
		foreach ($taskList as $task) {
			if (true !== $this->allowTaskApplied($task)) continue;
			$cacheId = max($cacheId, $task['taskid']);
			$r = $this->doApply($task);
			if ($r instanceof PwError) continue;
			$notice->sendNotice($this->uid, 'task', $this->uid, $task);
			if ($task['period']) $periodCache[] = $task['taskid'];
		}
		$cache = $this->_getTaskDs()->getTaskCacheByUid($this->uid);
		if (!$cache) $cache = array(0, array());
		$cacheId && $cache[0] = $cacheId;
		$cache[1] = array_diff($cache[1], $periodCache);
		return $this->_getTaskDs()->updateUserTaskCache($this->uid, $cache);
	}

	/**
	 * 添加一个任务申领
	 *
	 * @param array $task
	 * @return Ambigous <boolean, PwError>
	 */
	private function doApply($task) {
		$dm = new PwTaskUserDm();
		$dm->setTaskid($task['taskid'])->setUid($this->uid)->setTaskStatus(1)->setIsPeriod(
			$task['period'] > 0 ? 1 : 0)->setCreatedTime(Pw::getTime());
		$r = $this->_getTaskUserDs()->replaceUserTask($dm);
		if ($r instanceof PwError) return $r;
		return $this->_getTaskDs()->get($task['taskid']);
	}

	/**
	 * 判断任务是否允许申请
	 *
	 * 1、判断该任务是否能够被当前用户申领：
	 * 1-1：是否没有过期
	 * 1-2：该任务是否已经启用
	 * 1-3: 该任务是否已经开始
	 * 1-4：该用户的用户组是否允许申请该任务
	 * 1-5：该任务如果有前置任务，该前置任务是否已经被完成。
	 * 1-6：该用户是否已申领过该任务（该任务非周期任务）
	 * 1-7：该任务为周期任务，该用户是否已完成该任务，并且该任务周期是否已达到下次申领时间。
	 * 
	 * @param array $task 任务信息
	 * @return boolean|PwError
	 */
	private function allowTaskApplied($task) {
		$time = Pw::getTime();
		if ($task['end_time'] && ($task['end_time'] < $time)) {
			return new PwError('TASK:overtime');
		}
		if ($task['is_open'] == 0) {
			return new PwError('TASK:close');
		}
		if ($task['start_time'] && ($task['start_time'] > $time)) {
			return new PwError('TASK:no.open');
		}
		$gids = explode(',', $task['user_groups']);
		if (!array_intersect($gids, $this->gids)) {
			return new PwError('TASK:no.right');
		}
		/*前置任务判断*/
		if ($task['pre_task']) {
			$pre_taskApply = $this->_getTaskUserDs()->get($this->uid, $task['pre_task']);
			$pre_task = $this->_getTaskDs()->get($task['pre_task']);
			if (!$pre_taskApply) return new PwError('TASK:pre_task.require', 
				array('{title}' => $pre_task['title']));
			if (4 != $pre_taskApply['task_status']) return new PwError('TASK:pre_task.no.complete', 
				array('{title}' => $pre_task['title']));
		}
		/*该任务没有被申请过*/
		$taskApplied = $this->_getTaskUserDs()->get($this->uid, $task['taskid']);
		if (!$taskApplied) return true;
		/*如果该任务已经申请并且该任务非周期任务*/
		if (intval($taskApplied['is_period']) === 0) {
			return new PwError('TASK:already.apply');
		}
		/*如果该任务是周期任务，但是该任务没有完成*/
		if (4 != $taskApplied['task_status']) {
			return new PwError('TASK:already.apply.no.complete');
		}
		/*如果该周期任务已经完成，但是没有达到周期*/
		$periodTime = $task['period'] * 3600 + $taskApplied['finish_time'];
		if ($periodTime > Pw::getTime()) {
			return new PwError('TASK:apply.period.no.complete', 
				array('{time}' => Pw::time2str($periodTime, 'Y-m-d H:i:s')));
		}
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