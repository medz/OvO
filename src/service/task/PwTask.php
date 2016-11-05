<?php
Wind::import('SRV:task.dm.PwTaskDm');
/**
 * 任务体系的data service
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTask.php 18748 2012-09-27 03:45:32Z xiaoxia.xuxx $
 * @package service.task
 */
class PwTask {

	/**
	 * 添加pw_task表记录
	 *
	 * @param PwTaskDm $dm
	 * @return PwError|int
	 */
	public function addTask($dm) {
		if (!$dm instanceof PwTaskDm) return new PwError('TASK:datamodel.illegal');
		if (($r = $dm->beforeAdd()) instanceof PwError) return $r;
		//添加pw_task表记录
		$id = $this->_taskDao()->add($dm->getData());
		if (!$id) return new PwError('TASK:addtask.fail');
		/*如果开启状态，则将该数据添加到group表*/
		if (1 == $dm->getField('is_open')) {
			$dm->setTaskId($id);
			$result = $this->_taskGroupDao()->batchReplaceTaskGroups($dm->getTaskGroupData());
			if ($result instanceof PwError) return $result;
		}
		return $id;
	}

	/**
	 * 更新一条任务信息（for pw_task表）
	 *
	 * @param int $id
	 * @param PwTaskDm $dm
	 * @return PwError|boolean
	 */
	public function updateTask($dm) {
		if (!$dm instanceof PwTaskDm) return new PwError('TASK:datamodel.illegal');
		if (($r = $dm->beforeUpdate()) instanceof PwError) return $r;
		//更新pw_task表记录
		$this->_taskDao()->update($dm->getTaskId(), $dm->getData());
		$this->_taskGroupDao()->deleteByTaskId($dm->getTaskId());
		if (1 == $dm->getField('is_open')) return $this->_taskGroupDao()->batchReplaceTaskGroups(
			$dm->getTaskGroupData());
		return true;
	}

	/**
	 * 更新用户缓存表
	 *
	 * @param int $uid
	 * @param array $cache array($last_id, array($id1, $id2,..))
	 * @return PwError|boolean
	 */
	public function updateUserTaskCache($uid, $cache = array(0, array())) {
		if (0 >= ($uid = intval($uid))) return new PwError('TASK:param.illegal');
		return $this->_taskCacheDao()->update(
			array('uid' => $uid, 'task_ids' => serialize($cache)));
	}

	/**
	 * 删除一条任务
	 *
	 * @param int $id
	 * @return PwError|boolean
	 */
	public function deleteTask($id) {
		if (0 >= ($id = intval($id))) return new PwError('TASK:id.illegal');
		//删除pw_task表记录
		$this->_taskDao()->delete($id);
		return $this->_taskGroupDao()->deleteByTaskId($id);
	}

	/**
	 * 获取任务列表
	 *
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function getTaskList($num = 10, $start = 0) {
		return $this->_taskDao()->getTaskList((int) $num, (int) $start);
	}
	
	/**
	 * 根据任务ID列表获取该任务的下级任务
	 *
	 * @param array $taskIds
	 * @return array
	 */
	public function fetchNextTaskList($taskIds) {
		if (!$taskIds) return array();
		return $this->_taskDao()->fetchNextTaskList($taskIds);
	}

	/**
	 * 获取一条记录
	 *
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		return $this->_taskDao()->get((int) $id);
	}

	/**
	 * 获取一条或多条任务信息
	 *
	 * @param array $ids
	 * @return array
	 */
	public function gets($ids) {
		if (empty($ids)) return array();
		return $this->_taskDao()->fetch((array) $ids);
	}

	/**
	 * 获取id不在此范围内的可申请任务列表
	 *
	 * @param array $no_periods 用户已进行或已完成的非周期性任务id
	 * @param array $gids
	 * @param int $start
	 * @param int $num
	 * @param int $endTime 
	 * @return array
	 */
	public function getApplicableTasks($no_periods, $gids, $num = 10, $start = 0, $endTime) {
		//查询pw_task_group中不在这些id中的记录
		return $this->_taskGroupDao()->getApplicableTasks($no_periods, $gids, (int)$num, (int)$start, (int)$endTime);
	}

	/**
	 * 根据用户id查询用户任务的缓存
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getTaskCacheByUid($uid) {
		if (0 >= ($uid = intval($uid))) return array();
		$result = $this->_taskCacheDao()->get($uid);
		return unserialize($result);
	}

	/**
	 * 获取通过通过自动申请过滤的任务id
	 *
	 * @param int $last_id 上次自动申请的任务id缓存
	 * @param array $gids
	 * @param int $limit 返回条数
	 * @param int $endTime
	 * @return array
	 */
	public function getAutoApplicableTask($last_id, $gids, $limit = 1, $endTime = 0) {
		$last_id = intval($last_id);
		if (!is_array($gids)) return array();
		return $this->_taskGroupDao()->getAutoApplicableTask($last_id, $gids, (int) $limit, 
			(int) $endTime);
	}

	/**
	 * 获取id不在此范围内的可申请任务列表
	 *
	 * @param int $pre_id
	 * @param int $startTime
	 * @param int $endTime
	 * @return array
	 */
	public function getNextAutoTasks($pre_id, $startTime, $endTime) {
		if (0 >= ($pre_id = intval($pre_id))) return array();
		return $this->_taskDao()->getNextAutoTasks($pre_id, (int) $startTime, (int) $endTime);
	}

	/**
	 * 获取所有任务
	 *
	 * @return array
	 */
	public function getAll() {
		return $this->_taskDao()->getAll();
	}

	/**
	 * 统计任务数
	 *
	 * @return int
	 */
	public function countAll() {
		return $this->_taskDao()->count();
	}

	/**
	 * 获取id不在此范围内的可申请任务数量
	 *
	 * @param array $noIds
	 * @param array $gids
	 * @param int $endTime
	 */
	public function countApplicableTasks($noIds = array(), $gids, $endTime) {
		return $this->_taskGroupDao()->countApplicableTasks($noIds, $gids, (int) $endTime);
	}

	/**
	 * @return PwTaskCacheDao
	 */
	private function _taskCacheDao() {
		return Wekit::loadDao('task.dao.PwTaskCacheDao');
	}

	/**
	 * @return PwTaskGroupDao
	 */
	private function _taskGroupDao() {
		return Wekit::loadDao('task.dao.PwTaskGroupDao');
	}

	/**
	 * @return PwTaskDao
	 */
	private function _taskDao() {
		return Wekit::loadDao('task.dao.PwTaskDao');
	}
}

?>