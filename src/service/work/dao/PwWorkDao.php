<?php

/**
 * 工作经历Dao
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwWorkDao.php 1532 2012-1-10下午03:21:54 xiaoxiao $
 * @package src.service.user.hooks.experience_work.dao
 */
class PwWorkDao extends PwBaseDao {
	protected $_table = 'user_work';
	protected $_dataStruct = array('id', 'uid', 'company', 'starty', 'startm', 'endy', 'endm');
	
	/** 
	 * 添加工作经历
	 *
	 * @param array $data
	 * @return boolean|int
	 */
	public function add($data) {
		if (!($data = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		$smt = $this->getConnection()->execute($sql);
		return $this->getConnection()->lastInsertId();
	}
	
	/** 
	 * 更新工作经历
	 *
	 * @param int $id  工作经历ID
	 * @param int $uid 用户ID
	 * @param array $data
	 * @return boolean|int
	 */
	public function update($id, $uid, $data) {
		if (!($data = $this->_filterStruct($data))) return false;
		unset($data['uid']);
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE `id` = ? AND `uid` = ?', $this->getTable(), $this->sqlSingle($data));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($id, $uid));
	}
	
	/** 
	 * 删除工作经历
	 *
	 * @param int $id  工作经历ID
	 * @param int $uid 对应用户ID
	 * @return boolean|int
	 */
	public function delete($id, $uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `id` =? AND `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($id, $uid));
	}

	/** 
	 * 根据工作经历ID获取该工作经历详细信息
	 *
	 * @param int $id 经历ID
	 * @param int $uid 用户ID
	 * @return array
	 */
	public function get($id, $uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `id` =? AND `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($id, $uid));
	}
	
	/** 
	 * 根据用户ID删除用户工作经历
	 *
	 * @param int $uid
	 * @return boolean|int
	 */
	public function deleteByUid($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` =?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
	
	/** 
	 * 根据用户ID获得该用户的工作经历列表
	 *
	 * @param int $uid 用户ID
	 * @param int $limit 返回条数
	 * @param int $start 开始位置
	 * @return array
	 */
	public function getByUid($uid, $limit, $start) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` =? ORDER BY `starty` DESC, `startm` DESC' . $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'id');
	}
	
	/** 
	 * 根据用户ID统计该用户的工作经历
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countByUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `uid` =?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}
}