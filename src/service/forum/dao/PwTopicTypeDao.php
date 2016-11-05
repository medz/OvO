<?php
/**
 * @author peihong <jhqblxt@gmail.com> Nov 23, 2011
 * @link
 * @license
 * @version $Id: PwTopicTypeDao.php 16808 2012-08-28 10:05:30Z peihong.zhangph $
 */

class PwTopicTypeDao extends PwBaseDao {
	
	protected $_table = 'bbs_topic_type';
	protected $_dataStruct = array('fid', 'name', 'parentid','logo', 'vieworder', 'issys');
	
	public function addTopicType($fields){
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindTable('INSERT INTO %s SET ') . $this->sqlSingle($fields);
		$this->getConnection()->execute($sql);
		return $this->getConnection()->lastInsertId();
	}

	public function updateTopicType($id, $fields){
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindTable('UPDATE %s SET ') . $this->sqlSingle($fields) . ' WHERE id=?';
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($id));
	}
	
	public function getTopicTypesByFid($fid){
		$sql = $this->_bindTable('SELECT * FROM %s WHERE fid=? ORDER BY vieworder ASC');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'id');
	}
	
	public function getTopicType($id){
		$sql = $this->_bindTable('SELECT * FROM %s WHERE id=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($id));
	}
	
	public function fetchTopicType($ids) {
		return $this->_fetch($ids,'id');
	}
	
	public function deleteTopicType($id){
		$sql = $this->_bindTable('DELETE FROM %s WHERE id=?');
		return $this->getConnection()->createStatement($sql)->update(array($id));
	}
	
	public function deleteTopictypeByFid($fid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE fid=?');
		return $this->getConnection()->createStatement($sql)->update(array($fid));
	}
	
	public function deleteTopicTypesByParentid($parentid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE parentid=?');
		return $this->getConnection()->createStatement($sql)->update(array($parentid));
	}
}