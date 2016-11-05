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
class PwAttentionRecommendFriendsDao extends PwBaseDao {
	protected $_Attentiontable = 'attention';
	protected $_table = 'attention_recommend_friends';
	protected $_dataStruct = array('uid', 'recommend_uid', 'recommend_username', 'cnt', 'recommend_user');

	
	public function get($uid,$limit,$offset){
		$sql = $this->_bindSql("SELECT * FROM %s WHERE `uid` =?  ORDER BY `cnt` DESC %s", $this->getTable(), $this->sqlLimit($limit, $offset));
		$result = $this->getConnection()->createStatement($sql);
		return $result->queryAll(array($uid));
	}
	
	public function getSameUser($uid,$recommendUid){
		$sql = $this->_bindSql("SELECT * FROM %s WHERE `uid` =? AND `recommend_uid`=?", $this->getTable());
		$result = $this->getConnection()->createStatement($sql);
		return $result->getOne(array($uid,$recommendUid));
	}
	
	public function getRecommend($uid) {
		$sql = $this->_bindSql("SELECT a.uid,b.touid as recommend_uid,count(*) as cnt,b.uid AS same_uids FROM `pw_attention` a left join `pw_attention` b ON a.touid = b.uid  where a.uid = 82 GROUP BY recommend_uid", $this->getTable(), $this->sqlLimit($limit, $offset));
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
			$_temp['recommend_username'] = $_item['recommend_username'];
			$_temp['cnt'] = $_item['cnt'];
			$_temp['recommend_user'] = $_item['recommend_user'];
			$fields[] = $_temp;
		}
		if (!$fields) return false;
		$sql = $this->_bindSql('INSERT INTO %s (`uid`, `recommend_uid`, `recommend_username`, `cnt`, `recommend_user`) VALUES %s', $this->getTable(), $this->sqlMulti($fields));
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
	
	public function delete($uid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid));
	}
		
	public function deleteByRecommend($uid, $recommendUid){
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND recommend_uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $recommendUid));
	}
}