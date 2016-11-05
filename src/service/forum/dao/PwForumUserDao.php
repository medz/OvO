<?php

/**
 * 版块dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwForumUserDao.php 8503 2012-04-19 09:37:20Z jieyin $
 * @package forum
 */

class PwForumUserDao extends PwBaseDao {
	
	protected $_table = 'bbs_forum_user';
	protected $_dataStruct = array('uid', 'fid', 'join_time');
	
	public function get($uid, $fid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid, $fid));
	}
	
	public function getUserByFid($fid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE fid=? ORDER BY join_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'uid');
	}

	public function countUserByFid($fid) {
		$sql = $this->_bindTable('SELECT COUNT(*) AS count FROM %s WHERE fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($fid));
	}

	public function getFroumByUid($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'fid');
	}

	public function add($data) {
		return $this->_add($data, false);
	}

	public function delete($uid, $fid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $fid));
	}
	/*
	public function getForum($fid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($fid));
	}

	public function searchForum($keyword){
		$sql = $this->_bindTable('SELECT fid,name FROM %s WHERE name LIKE ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array("$keyword%"));
	}
	
	public function getForumList() {
		$sql = $this->_bindTable('SELECT * FROM %s ORDER BY issub ASC,vieworder ASC');
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('fid');
	}

	public function getCommonForumList() {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE issub=0 ORDER BY vieworder ASC');
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('fid');
	}

	public function getForumByFids($fids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE fid IN %s', $this->getTable(), $this->sqlImplode($fids));
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('fid');
	}

	public function getSubForums($fid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE parentid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'fid');
	}

	public function addForum($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return $this->getConnection()->lastInsertId();
	}

	public function updateForum($fid, $fields, $increaseFields = array()) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE fid=?', $this->getTable(), $this->sqlSingle($fields));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($fid));
	}

	public function batchUpdateForum($fids, $fields, $increaseFields = array()) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE fid IN %s', $this->getTable(), $this->sqlSingle($fields), $this->sqlImplode($fids));
		$this->getConnection()->execute($sql);
		return true;
	}

	public function deleteForum($fid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($fid));
	}*/
}