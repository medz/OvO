<?php
/**
 * 用户关注数据表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttentionDao.php 22660 2012-12-26 07:45:31Z jinlong.panjl $
 * @package src.service.user.dao
 */
class PwAttentionDao extends PwBaseDao {

	protected $_table = 'attention';
	protected $_dataStruct = array('uid', 'touid', 'created_time');
	
	public function get($uid, $touid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND touid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid, $touid));
	}

	public function add($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return true;
	}

	public function delete($uid, $touid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND touid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $touid));
	}

	public function getFans($uid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE touid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'uid');
	}

	public function fetchFans($uid, $touids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE touid=? AND uid IN %s', $this->getTable(), $this->sqlImplode($touids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'uid');
	} 

	public function fetchFansByUids($uids, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE touid IN %s %s ', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(), 'uid');
	} 
	
	public function getFollows($uid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'touid');
	}

	public function fetchFollows($uid, $touids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? AND touid IN %s', $this->getTable(), $this->sqlImplode($touids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'touid');
	} 

	public function countFollowToFollow($uid, $touid) {
		$sql = $this->_bindSql('SELECT COUNT(*) AS sum FROM %s a LEFT JOIN %s b ON a.touid=b.uid WHERE a.uid=? AND b.touid=?', $this->getTable(), $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid, $touid));
	}

	public function getFollowToFollow($uid, $touid, $limit) {
		$sql = $this->_bindSql('SELECT a.touid FROM %s a LEFT JOIN %s b ON a.touid=b.uid WHERE a.uid=? AND b.touid=? ORDER BY b.created_time DESC %s', $this->getTable(), $this->getTable(), $this->sqlLimit($limit));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid, $touid), 'touid');
	}

	public function getFriendsByUid($uid){
		$sql = $this->_bindSql("SELECT a.uid,b.touid as recommend_uid,b.uid AS same_uid FROM %s a left join %s b ON a.touid = b.uid  where a.uid =? GROUP BY recommend_uid, same_uid", $this->getTable(), $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid));
	}
	
	public function fetchFriendsByUids($uids){
		$sql = $this->_bindSql("SELECT uid, group_concat( touid SEPARATOR ',' ) AS touids FROM %s WHERE uid IN %s GROUP BY uid",$this->getTable(),$this->sqlImplode($uids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(), 'uid');
	}
}