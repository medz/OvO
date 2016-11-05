<?php

/**
 * 可能认识的人DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwAttentionRecommendRecordDao extends PwBaseDao {
	protected $_table = 'attention_recommend_record';
	protected $_dataStruct = array('uid', 'recommend_uid', 'same_uid');

	
	public function getRecommendFriend($uid,$limit,$offset){
		$sql = $this->_bindSql("SELECT uid,recommend_uid,group_concat(same_uid) as same_uids,count(same_uid) as cnt FROM %s WHERE `uid` =? GROUP BY recommend_uid ORDER BY cnt DESC %s", $this->getTable(), $this->sqlLimit($limit, $offset));
		$result = $this->getConnection()->createStatement($sql);
		return $result->queryAll(array($uid));
	}

	public function batchReplace($data) {
		$fields = array();
		foreach ($data as $_item) {
			if (!($_item = $this->_filterStruct($_item))) continue;
			$_temp = array();
			$_temp['uid'] = $_item['uid'];
			$_temp['recommend_uid'] = $_item['recommend_uid'];
			$_temp['same_uid'] = $_item['same_uid'];
			$fields[] = $_temp;
		}
		if (!$fields) return false;
		$sql = $this->_bindSql('REPLACE INTO %s (`uid`, `recommend_uid`, `same_uid`) VALUES %s', $this->getTable(), $this->sqlMulti($fields));
		return $this->getConnection()->execute($sql);
	}

	public function replace($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return true;
	}
	
	public function deleteRecommendFriendByUid($uid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
		
	public function deleteByUidAndSameUid($uid, $same){
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND same_uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $same));
	}
	
	public function deleteRecommendFriend($uid, $recommendUid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND recommend_uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $recommendUid));
	}
}