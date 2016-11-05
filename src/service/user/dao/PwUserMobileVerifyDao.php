<?php

/**
 * 手机验证
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwUserMobileVerifyDao extends PwBaseDao {
	protected $_table = 'user_mobile_verify';
	protected $_pk = 'mobile';
	protected $_dataStruct = array('mobile', 'code', 'expired_time', 'number', 'create_time');

	/**
	 * 取一条
	 *
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		return $this->_get($id);
	}
	
	/**
	 * 批量取
	 *
	 * @param array $ids
	 * @return array
	 */
	public function fetch($ids) {
		return $this->_fetch($ids);
	}
	
	/**
	 * 添加单条
	 * 
	 * @param array $fields
	 * @return bool 
	 */
	public function add($fields) {
		return $this->_add($fields);
	}
	
	/**
	 * 添加单条
	 * 
	 * @param array $fields
	 * @return bool 
	 */
	public function replace($fields) {
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 删除单条
	 * 
	 * @param int $id
	 * @return bool 
	 */
	public function delete($id) {
		return $this->_delete($id);
	}
	
	/**
	 * 删除单条
	 * 
	 * @param int $id
	 * @return bool 
	 */
	public function deleteByExpiredTime($expired_time) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `expired_time`<?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($expired_time));
	}
	
	/**
	 * 批量删除
	 * 
	 * @param array $ids
	 * @return bool 
	 */
	public function batchDelete($ids) {
		return $this->_batchDelete($ids);
	}
	
	/**
	 * 更新单条
	 * 
	 * @param int $id
	 * @param array $fields
	 * @return bool 
	 */
	public function update($id,$fields) {
		return $this->_update($id, $fields);
	}
	
	/**
	 * 更新单条
	 * 
	 * @param int $expiredTime
	 * @param array $fields
	 * @return bool 
	 */
	public function updateByExpiredTime($expiredTime, $fields) {
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE `expired_time`<?', $this->getTable(), $this->sqlSingle($fields));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($expiredTime));
	}
	
	/**
	 * 批量更新
	 * 
	 * @param array $ids
	 * @param array $fields
	 * @return bool 
	 */
	public function batchUpdate($ids,$fields) {
		return $this->_batchUpdate($ids, $fields);
	}
}