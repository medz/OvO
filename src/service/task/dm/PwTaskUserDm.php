<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * 用户任务模型
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskUserDm.php 5463 2012-03-05 08:45:22Z long.shi $
 * @package service.task.dm
 */
class PwTaskUserDm extends PwBaseDm {

	/**
	 * 设置uid
	 *
	 * @param int $uid
	 * @return PwTaskUserDM
	 */
	public function setUid($uid) {
		$this->_data['uid'] = (int) $uid;
		return $this;
	}

	/**
	 * 设置任务id
	 *
	 * @param int $taskid
	 * @return PwTaskUserDM
	 */
	public function setTaskid($taskid) {
		$this->_data['taskid'] = (int) $taskid;
		return $this;
	}

	/**
	 * 设置用户任务状态
	 *
	 * @param int $taskStatus
	 * @return PwTaskUserDM
	 */
	public function setTaskStatus($taskStatus) {
		$this->_data['task_status'] = (int) $taskStatus;
		return $this;
	}

	/**
	 * 设置任务是否周期性
	 *
	 * @param int $is_period
	 * @return PwTaskUserDM
	 */
	public function setIsPeriod($is_period) {
		$this->_data['is_period'] = (int) $is_period;
		return $this;
	}

	/**
	 * 设置任务完成度
	 *
	 * @param string $step
	 * @return PwTaskUserDM
	 */
	public function setStep($step) {
		$this->_data['step'] = trim($step);
		return $this;
	}

	/**
	 * 设置任务完成时间
	 *
	 * @param int $finish_time
	 * @return PwTaskUserDM
	 */
	public function setFinishTime($finish_time) {
		$this->_data['finish_time'] = intval($finish_time);
		return $this;
	}

	/**
	 * 设置任务创建时间
	 *
	 * @param int $created_time
	 * @return PwTaskUserDM
	 */
	public function setCreatedTime($created_time) {
		$this->_data['created_time'] = intval($created_time);
		return $this;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (!$this->_data['uid'] || !$this->_data['taskid']) return new PwError('TASK:id.illegal');
	}

}

?>