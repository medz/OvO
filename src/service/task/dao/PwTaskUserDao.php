<?php
Wind::import('LIB:base.PwBaseDao');
/**
 * Pw_task_user的dao
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskUserDao.php 16928 2012-08-29 08:54:39Z xiaoxia.xuxx $
 * @package service.task.dao
 */
class PwTaskUserDao extends PwBaseDao {
	protected $_table = 'task_user';
	protected $_dataStruct = array(
		'taskid', 
		'uid', 
		'task_status', 
		'is_period', 
		'step', 
		'created_time', 
		'finish_time');

	/**
	 * 添加一条用户任务记录
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function replaceUserTask($data) {
		if (!$data = $this->_filterStruct($data)) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}

	/**
	 * 更新任务是否周期性
	 *
	 * @param int $taskid
	 * @param int $is_period
	 * @return boolean
	 */
	public function updateIsPeriod($taskid, $data) {
		if (!isset($data['period'])) return false;
		$is_period = intval($data['period']) > 0 ? 1 : 0; 
		$sql = $this->_bindTable('UPDATE %s SET `is_period` = ? WHERE `taskid` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($is_period, $taskid));
	}

	/**
	 * 修改记录
	 *
	 * @param int $id
	 * @param int $uid
	 * @param array $data
	 * @return boolean
	 */
	public function updateByTaskIdAndUid($taskid, $uid, $data) {
		if (!$data = $this->_filterStruct($data)) return false;
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE `taskid` = ? AND `uid` = ?', 
			$this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->createStatement($sql)->update(array($taskid, $uid));
	}

	/**
	 * 获取用户的某个任务的具体信息
	 *
	 * @param int $taskid
	 * @param int $uid
	 * @return array
	 */
	public function get($taskid, $uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `taskid` = ? AND `uid` = ?');
		return $this->getConnection()->createStatement($sql)->getOne(array($taskid, $uid));
	}

	/**
	 * 批量查询用户任务
	 *
	 * @param array $ids
	 * @param int $uid
	 * @return array
	 */
	public function gets($ids, $uid) {
		$sql = $this->_bindTable(
			'SELECT * FROM %s WHERE `uid` = ? AND `taskid` IN ' . $this->sqlImplode((array) $ids));
		return $this->getConnection()->createStatement($sql)->queryAll(array($uid), 'taskid');
	}

	/**
	 * 根据用户和任务状态查询任务
	 *
	 * @param int $uid
	 * @param int $status
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function getMyTasksByStatus($uid, $status, $num = 10, $start = 0) {
		$order = $status == 4 ? 'finish_time' : 'created_time';
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `uid` = ? AND `task_status` & ? ORDER BY `%s` DESC %s', $this->getTable(), $order, $this->sqlLimit($num, $start));
		return $this->getConnection()->createStatement($sql)->queryAll(array($uid, $status), 'taskid');
	}

	/**
	 * 根据是否周期性查询用户任务
	 *
	 * @param int $uid
	 * @param int $isPeriod
	 * @return array
	 */
	public function getTasksByIsPeriod($uid, $isPeriod) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` = ? AND `is_period` = ?');
		return $this->getConnection()->createStatement($sql)->queryAll(array($uid, $isPeriod), 
			'taskid');
	}

	/**
	 * 根据uid和状态查询总数
	 *
	 * @param int $uid
	 * @param int $status
	 * @return int
	 */
	public function countMyTasksByStatus($uid, $status) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `uid` = ? AND `task_status` & ?');
		return $this->getConnection()->createStatement($sql)->getValue(array($uid, $status));
	}

	/**
	 * 根据任务id删除记录
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteByTaskid($id) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `taskid` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($id));
	}

	/**
	 * 根据用户id删除记录
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteByUid($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($uid));
	}
	
	/**
	 * 根据用户ID列表批量删除信息
	 *
	 * @param array $uids
	 * @return int
	 */
	public function batchDeleteByUid($uids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` IN %s', $this->getTable(), $this->sqlImplode($uids));
		return $this->getConnection()->execute($sql);
	}
}
?>