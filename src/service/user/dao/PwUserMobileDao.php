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
class PwUserMobileDao extends PwBaseDao {
	protected $_table = 'user_mobile';
	protected $_pk = 'uid';
	protected $_dataStruct = array('uid', 'mobile');

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
	 * 根据手机号码取一条
	 *
	 * @param int $mobile
	 * @return array
	 */
	public function getByMobile($mobile) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `mobile`=?', $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($mobile));
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
	 * 删除单条
	 * 
	 * @param int $id
	 * @return bool 
	 */
	public function delete($id) {
		return $this->_delete($id);
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