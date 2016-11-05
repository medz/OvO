<?php
/**
 * 用户任务的数据服务
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskUser.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package service.task
 */
class PwTaskUser {
	
	/*用户任务状态值：1:正在进行中的*/
	const DOING = 1;
	/*用户任务状态值：2：已经完成等待领取奖励的*/
	const UNREWARD = 2;
	/*用户任务状态值：4：已经完成的任务*/
	const COMPLETE = 4;

	/**
	 * 添加或修改一个用户任务
	 *
	 * @param PwTaskUserDm $data
	 * @return boolean
	 */
	public function replaceUserTask(PwTaskUserDm $dm) {
		if (($r = $dm->beforeUpdate()) instanceof PwError) return $r;
		return $this->_taskUserDao()->replaceUserTask($dm->getData());
	}

	/**
	 * 更新一条用户任务
	 *
	 * @param int $taskid
	 * @param int $uid
	 * @param PwTaskUserDm $dm
	 * @return PwError|boolean
	 */
	public function update($taskid, $uid, PwTaskUserDm $dm) {
		if (0 >= ($taskid = intval($taskid)) || 0 >= ($uid = intval($uid))) return new PwError(
			'TASK:id.illegal');
		return $this->_taskUserDao()->updateByTaskIdAndUid($taskid, $uid, $dm->getData());
	}

	/**
	 * 根据任务id删除pw_task_user表记录
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function delete($taskid) {
		if (0 >= ($taskid = intval($taskid))) return new PwError('TASK:id.illegal');
		return $this->_taskUserDao()->deleteByTaskid($taskid);
	}

	/**
	 * 根据用户id删除pw_task_user记录
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteByUid($uid) {
		if (($uid = intval($uid)) < 1) return false;
		return $this->_taskUserDao()->deleteByUid($uid);
	}

	/**
	 * 根据用户id删除pw_task_user记录
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function batchDeleteByUid($uids) {
		if (empty($uids)) return false;
		return $this->_taskUserDao()->batchDeleteByUid($uids);
	}
	
	/**
	 * 根据是否周期性查询用户任务
	 *
	 * @param int $uid
	 * @param int $isPeriod 1代表周期性任务，0代表非周期性任务
	 * @return array
	 */
	public function getByIsPeriod($uid, $isPeriod) {
		if (0 >= ($uid = intval($uid)) || !in_array((int) $isPeriod, array(0, 1))) return array();
		return $this->_taskUserDao()->getTasksByIsPeriod($uid, $isPeriod);
	}

	/**
	 * 取一条任务记录
	 *
	 * @param int $uid
	 * @param int $taskid
	 * @return array
	 */
	public function get($uid, $taskid) {
		if (0 >= ($uid = intval($uid)) || 0 >= ($taskid = intval($taskid))) return array();
		return $this->_taskUserDao()->get($taskid, $uid);
	}

	/**
	 * 取多条用户任务
	 *
	 * @param int $uid
	 * @param array $taskids
	 * @return array
	 */
	public function gets($uid, $taskids) {
		if (empty($taskids)) return array();
		return $this->_taskUserDao()->gets((array) $taskids, $uid);
	}

	/**
	 * 根据任务状态，获取我的任务列表
	 *
	 * @param int $uid
	 * @param status 用户任务状态 （使用位运算，如3表示已申请+完成任务未领奖励）
	 * 1 表示已申请，
	 * 2 表示完成任务未领奖励，
	 * 4 表示已领奖励
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function getMyTaskByStatus($uid, $status, $num = 10, $start = 0) {
		return $this->_taskUserDao()->getMyTasksByStatus($uid, $status, (int)$num, (int)$start);
	}

	/**
	 * 根据状态查询我的任务总数
	 *
	 * @param int $uid
	 * @param int $status 同@method getMyTaskByStatus
	 * 1 表示已申请，
	 * 2 表示完成任务未领奖励，
	 * 4 表示已领奖励
	 * @return int
	 */
	public function countMyTasksByStatus($uid, $status) {
		if (0 > ($status = intval($status)) || 0 >= ($uid = intval($uid))) return 0;
		return $this->_taskUserDao()->countMyTasksByStatus($uid, $status);
	}

	/**
	 * @return PwTaskUserDao
	 */
	private function _taskUserDao() {
		return Wekit::loadDao('task.dao.PwTaskUserDao');
	}
}

?>