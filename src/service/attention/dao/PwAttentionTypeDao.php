<?php
/**
 * 用户关注数据表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttentionTypeDao.php 7059 2012-03-29 05:20:59Z jieyin $
 * @package src.service.user.dao
 */
class PwAttentionTypeDao extends PwBaseDao {

	protected $_table = 'attention_type';
	protected $_dataStruct = array('id', 'uid', 'name');

	public function getType($id) {
		return $this->_get($id);
	}

	public function getTypeByUid($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'id');
	}
	
	/**
	 * 增加一个分类
	 *
	 * @param array $fields
	 * @return bool
	 */
	public function addType($fields) {
		return $this->_add($fields);
	}
	
	/**
	 * 修改一个分类
	 *
	 * @param array $fields
	 * @return bool
	 */
	public function editType($id, $fields) {
		return $this->_update($id, $fields);
	}

	/**
	 * 删除一条分类
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteType($id) {
		return $this->_delete($id);
	}
}