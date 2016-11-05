<?php
Wind::import('LIB:base.PwBaseDao');
/**
 * Pw_task_group的dao
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskGroupDao.php 9238 2012-05-04 03:12:52Z long.shi $
 * @package service.task.dao
 */
class PwTaskGroupDao extends PwBaseDao {
	protected $_table = 'task_group';
	protected $_dataStruct = array('taskid', 'gid', 'is_auto', 'end_time');

	/**
	 * 批量添加任务用户组信息
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function batchReplaceTaskGroups($data) {
		if (empty($data) || !is_array($data)) return false;
		$tmp = array();
		foreach ($data as $v) {
			$v = $this->_filterStruct($v);
			if ($v) $tmp[] = $v;
		}
		if (empty($tmp)) return false;
		$sql = $this->_bindSql('REPLACE INTO %s (`taskid`, `gid`, `is_auto`, `end_time`) VALUES %s', 
			$this->getTable(), $this->sqlMulti($tmp));
		return $this->getConnection()->execute($sql);
	}

	/**
	 * 根据任务id删除
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteByTaskId($id) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `taskid` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($id));
	}

	/**
	 * 获取id不在此范围内的可申请任务列表
	 *
	 * @param array $noIds
	 * @param int $num
	 * @param int $start
	 * @param int $endTime
	 * @return array
	 */
	public function getApplicableTasks($noIds = array(), $gids, $num = 10, $start = 0, $endTime) {
		$where = '1';
		if ($noIds) $where .= $this->_bindSql(' AND `taskid` NOT IN %s', 
			$this->sqlImplode((array) $noIds));
		$sql = $this->_bindSql(
			'SELECT distinct `taskid` FROM %s WHERE %s AND `gid` IN %s AND `end_time` > %s AND `is_auto` = 0 ORDER BY `taskid` DESC %s', 
			$this->getTable(), $where, $this->sqlImplode((array) $gids), $endTime, 
			$this->sqlLimit($num, $start));
		return $this->getConnection()->query($sql)->fetchAll('taskid');
	}

	/**
	 * 查询id不在此范围内的可申请任务数量
	 *
	 * @param array $noIds
	 * @param array $gids
	 * @param int $endTime
	 * @return int
	 */
	public function countApplicableTasks($noIds = array(), $gids, $endTime) {
		$where = '1';
		if ($noIds) $where .= $this->_bindSql(' AND `taskid` NOT IN %s', 
			$this->sqlImplode((array) $noIds));
		$sql = $this->_bindSql(
			'SELECT COUNT(distinct `taskid`) FROM %s WHERE %s AND `gid` IN %s AND `end_time` > %s AND `is_auto` = 0', 
			$this->getTable(), $where, $this->sqlImplode((array) $gids), $endTime);
		return $this->getConnection()->query($sql)->fetchColumn();
	}

	/**
	 * 获取通过自动申请过滤的任务
	 *
	 * @param int $last_id
	 * @param array $gids
	 * @param int $limit
	 * @param int $endTime
	 * @return int
	 */
	public function getAutoApplicableTask($last_id, $gids, $limit, $endTime) {
		$sql = $this->_bindSql(
			'SELECT `taskid` FROM %s WHERE `taskid` > ? AND `gid` IN %s AND `is_auto` = 1 AND `end_time` > %s ORDER BY `taskid` %s', 
			$this->getTable(), $this->sqlImplode((array) $gids), $endTime, $this->sqlLimit($limit));
		return $this->getConnection()->createStatement($sql)->queryAll(array($last_id), 'taskid');
	}
}
?>