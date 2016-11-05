<?php
/**
 * 用户关注数据表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshRelationDao.php 19501 2012-10-15 08:36:20Z jieyin $
 * @package src.service.user.dao
 */
class PwFreshRelationDao extends PwBaseDao {

	protected $_table = 'attention_fresh_relations';
	protected $_attentionTable = 'attention';
	protected $_dataStruct = array('uid', 'fresh_id', 'type', 'created_userid', 'created_time');
	
	public function get($uid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid));
	}

	public function fetchAttentionFreshByUid($uid, $uids, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? AND created_userid IN %s ORDER BY created_time DESC %s', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid));
	}
	
	public function count($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) AS count FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}

	public function addRelation($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return true;
	}

	public function addRelationByAttention($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('INSERT INTO %s (uid, fresh_id, type, created_userid, created_time) SELECT uid,?,?,?,? FROM %s WHERE touid=? ORDER BY created_time DESC LIMIT 1000', $this->getTable(), $this->getTable($this->_attentionTable));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($fields['fresh_id'], $fields['type'], $fields['created_userid'], $fields['created_time'], $fields['uid']));
	}

	public function batchAdd($fields) {
		$array = array();
		foreach ($fields as $key => $value) {
			$array[] = array($value['uid'], $value['fresh_id'], $value['type'], $value['created_userid'], $value['created_time']);
		}
		$sql = $this->_bindSql('INSERT INTO %s (uid, fresh_id, type, created_userid, created_time) VALUES %s', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}
	
	public function batchDelete($ids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE fresh_id IN %s', $this->getTable(), $this->sqlImplode($ids));
		$this->getConnection()->execute($sql);
		return true;
	}

	public function deleteByUidAndCreatedUid($uid, $fromuid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND created_userid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $fromuid));
	}

	public function deleteOver($uid, $limit) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE uid=? ORDER BY created_time ASC LIMIT %s', $this->getTable(), intval($limit));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
}