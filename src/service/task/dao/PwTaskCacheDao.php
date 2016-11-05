<?php
Wind::import('LIB:base.PwBaseDao');
/**
 * Pw_task_cache的dao
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskCacheDao.php 7210 2012-03-31 02:27:34Z long.shi $
 * @package service.task.dao
 */
class PwTaskCacheDao extends PwBaseDao {
	protected $_table = 'task_cache';
	protected $_dataStruct = array('uid', 'task_ids');

	/**
	 * 添加一条缓存记录
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function update($data) {
		if (!$data = $this->_filterStruct($data)) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}

	/**
	 * 根据uid获取缓存记录
	 *
	 * @param int $uid
	 * @return array
	 */
	public function get($uid) {
		$sql = $this->_bindTable('SELECT `task_ids` FROM %s WHERE `uid` = ?');
		return $this->getConnection()->createStatement($sql)->getValue(array($uid));
	}
}
?>