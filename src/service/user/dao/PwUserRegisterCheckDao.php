<?php

/**
 * 用户注册的审核/激活DS
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserRegisterCheckDao.php 7687 2012-04-10 11:17:58Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserRegisterCheckDao extends PwBaseDao {
	protected $_table = 'user_register_check';
	protected $_pk = 'uid';
	protected $_dataStruct = array('uid', 'ifchecked', 'ifactived');

	/**
	 * 根据用户ID获得用户的状态信息
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getInfo($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid));
	}
	
	/**
	 * 根据用户的审核状态获得用户的记录
	 *
	 * @param int $ifchecked
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function getInfoByIfchecked($ifchecked, $limit, $start) {
		$sql = $this->_bindSql("SELECT * FROM %s WHERE `ifchecked` =? %s", $this->getTable(), $this->sqlLimit($limit, $start));
		$result = $this->getConnection()->createStatement($sql);
		return $result->queryAll(array($ifchecked), 'uid');
	}
	
	/** 
	 * 获得没有激活用户的统计总数
	 *
	 * @param int $ifchecked
	 * @return int
	 */
	public function countByIfchecked($ifchecked) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `ifchecked` = ?');
		$result = $this->getConnection()->createStatement($sql);
		return $result->getValue(array($ifchecked));
	}
	
	/**
	 * 根据用户激活字段获得用户的记录
	 *
	 * @param int $ifactived
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function getInfoByIfactived($ifactived, $limit, $start) {
		$sql = $this->_bindSql("SELECT * FROM %s WHERE `ifactived` = ? %s", $this->getTable(), $this->sqlLimit($limit, $start));
		$result = $this->getConnection()->createStatement($sql);
		return $result->queryAll(array($ifactived), 'uid');
	}
	
	/** 
	 * 获得没有激活用户的统计总数
	 *
	 * @param int $ifactived
	 * @return int
	 */
	public function countByIfactived($ifactived) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `ifactived` = ?');
		$result = $this->getConnection()->createStatement($sql);
		return $result->getValue(array($ifactived));
	}
	
	/**
	 * 设置用户的状态
	 *
	 * @param int $uid 用户ID
	 * @param int $ifchecked 用户是否已审核
	 * @param int $ifactived 用户是否已经激活
	 * @return boolean
	 */
	public function addInfo($uid, $ifchecked, $ifactived) {
		$sql = $this->_bindTable('REPLACE INTO %s SET `ifchecked` = ?, `ifactived` = ?, `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($ifchecked, $ifactived, $uid));
	}
	
	/**
	 * 更新用户的状态
	 *
	 * @param int $uid 用户ID
	 * @param array $data 用户信息
	 * @return boolean
	 */
	public function updateInfo($uid, $data) {
		if (!($clear = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE `uid` = ?', $this->getTable(), $this->sqlSingle($clear));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
	
	/**
	 * 批量修改用户
	 *
	 * @param array $uids
	 * @param array $data
	 * @return boolean
	 */
	public function batchUpdateInfo($uids, $data) {
		if (!($clear = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE `uid` IN %s', $this->getTable(), $this->sqlSingle($clear), $this->sqlImplode($uids));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据用户ID删除该用户的状态信息
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteInfo($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
	
	/**
	 * 根据用户ID批量删除用户状态记录信息
	 *
	 * @param array $uids
	 * @return boolean
	 */
	public function batchDeleteInfo($uids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` IN %s', $this->getTable(), $this->sqlImplode($uids));
		return $this->getConnection()->execute($sql);
	}
}