<?php
/**
 * @author Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBehaviorDao.php 23501 2013-01-10 06:53:26Z jinlong.panjl $ 
 * @package 
 */
class PwUserBehaviorDao extends PwBaseDao {
	protected $_table = 'user_behavior';
	protected $_dataStruct = array('uid', 'behavior', 'number', 'expired_time','extend_info');
	
	public function getInfo($uid, $behavior) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? AND behavior = ? ');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid, $behavior));
	}
	
	public function fetchInfo($uids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN  %s ', $this->getTable(), $this->sqlImplode($uids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array());
	}
	
	public function getBehaviorList($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ? ');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'behavior');
	}
	
	public function replaceInfo($data) {
		if (!$data = $this->_filterStruct($data)) return false;
		if (!$data['uid'] || !$data['behavior']) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		$r = $this->getConnection()->execute($sql);
		PwSimpleHook::getInstance('PwUserBehaviorDao_replaceInfo')->runDo($data);
		return $r;
	}
	
	public function deleteInfo($uid) {
		$sql = $this->_bindTable('DELETE FROM %s  WHERE uid = ? ');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
	
	public function deleteInfoByUidBehavior($uid, $behavior) {
		$sql = $this->_bindTable('DELETE FROM %s  WHERE uid = ? AND behavior = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $behavior));
	}
	
}