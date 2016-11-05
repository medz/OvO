<?php
Wind::import('LIB:base.PwBaseDao');
/**
 * pw_task的dao
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskDao.php 18748 2012-09-27 03:45:32Z xiaoxia.xuxx $
 * @package service.task.dao
 */
class PwTaskDao extends PwBaseDao {
	protected $_table = 'task';
	protected $_pk = 'taskid';
	protected $_dataStruct = array(
		'taskid', 
		'pre_task', 
		'is_auto', 
		'is_display_all', 
		'view_order', 
		'is_open', 
		'start_time', 
		'end_time', 
		'period', 
		'title', 
		'description', 
		'icon', 
		'user_groups', 
		'reward', 
		'conditions');

	/**
	 * 添加一条任务
	 *
	 * @param array $data
	 * @return boolean|int
	 */
	public function add($data) {
		return $this->_add($data);
	}

	/**
	 * 更新一条任务
	 *
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function update($id, $data) {
		$result = $this->_update($id, $data);
		PwSimpleHook::getInstance('PwTaskDao_update')->runDo($id, $data);
		return $result;
	}

	/**
	 * 删除一条任务
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function delete($id) {
		return $this->_delete($id);
	}

	/**
	 * 根据id获取任务
	 *
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		return $this->_get($id);
	}

	/**
	 * 查询id在范围之内的列表
	 *
	 * @param array $ids
	 * @return array
	 */
	public function fetch($ids = array()) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `taskid` IN %s ORDER BY `taskid` DESC', $this->getTable(), $this->sqlImplode($ids));
		$rst = $this->getConnection()->query($sql);
		return $rst->fetchAll('taskid');
	}

	/**
	 * 获取任务列表，根据顺序排序
	 *
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function getTaskList($num = 10, $start = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s ORDER BY `view_order`, `taskid` DESC %s', 
			$this->getTable(), $this->sqlLimit($num, $start));
		return $this->getConnection()->query($sql)->fetchAll('taskid');
	}

	/**
	 * 获取所有任务的id和名称
	 *
	 * @return array
	 */
	public function getAll() {
		$sql = $this->_bindTable('SELECT * FROM %s limit 0,1000');
		return $this->getConnection()->query($sql)->fetchAll('taskid');
	}

	/**
	 * 查询某个自动任务的所有后置的自动任务
	 *
	 * @param int $pre_id
	 * @return array
	 */
	public function getNextAutoTasks($pre_id, $startTime, $endTime) {
		list($startTime, $endTime) = array(intval($startTime), intval($endTime));
		$where = '';
		if ($startTime) $where .= $this->_bindSql(' AND `start_time` < %s', $startTime);
		if ($endTime) $where .= $this->_bindSql(' AND `end_time` > %s', $endTime);
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `pre_task` = ? AND `is_auto` = 1 %s', 
			$this->getTable(), $where);
		return $this->getConnection()->createStatement($sql)->queryAll(array($pre_id), 'taskid');
	}
	
	/**
	 * 获得级联任务链的下一个任务
	 *
	 * @param array $pre_id
	 * @return array
	 */
	public function fetchNextTaskList($pre_id) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `pre_task` IN %s ',$this->getTable(), $this->sqlImplode($pre_id));
		return $this->getConnection()->createStatement($sql)->queryAll(array(), 'pre_task');
	}
	
	/**
	 * 统计任务数
	 *
	 * @return int
	 */
	public function count() {
		$sql = $this->_bindTable('SELECT COUNT(`taskid`) FROM %s');
		return $this->getConnection()->query($sql)->fetchColumn();
	}
}
?>