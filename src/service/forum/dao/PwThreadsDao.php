<?php

/**
 * 帖子dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadsDao.php 24251 2013-01-23 09:07:13Z jinlong.panjl $
 * @package forum
 */

class PwThreadsDao extends PwBaseDao {
	
	protected $_table = 'bbs_threads';
	protected $_pk = 'tid';
	protected $_dataStruct = array('tid', 'fid', 'topic_type', 'subject', 'topped', 'digest','overtime', 'highlight', 'inspect', 'ifshield', 'disabled', 'ischeck', 'replies', 'hits','like_count', 'special', 'tpcstatus', 'ifupload', 'created_time', 'created_username', 'created_userid', 'created_ip', 'modified_time', 'modified_username', 'modified_userid', 'modified_ip', 'lastpost_time', 'lastpost_userid', 'lastpost_username', 'reply_notice', 'reply_topped', 'special_sort', 'app_mark');
	
	public function getThread($tid) {
		return $this->_get($tid);
	}

	public function fetchThread($tids) {
		return $this->_fetch($tids, 'tid');
	}
	
	public function getThreadByFid($fid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE fid=? AND disabled=0 ORDER BY lastpost_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'tid');
	}
	
	public function fetchThreadByTid($tids, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s AND disabled=0 ORDER BY special_sort DESC, lastpost_time DESC %s', $this->getTable(), $this->sqlImplode($tids), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(), 'tid');
	}

	public function countPosts($fid) {
		$sql = $this->_bindTable('SELECT COUNT(*) AS topics,SUM( replies ) AS replies FROM %s WHERE fid=? AND disabled=0');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($fid));
	}
	
	public function getThreadByFidAndType($fid, $type, $limit, $start) {
		$sql = 'SELECT * FROM %s WHERE fid=? AND topic_type=? AND disabled=0 ORDER BY lastpost_time DESC' . $this->sqlLimit($limit, $start);
		$sql = $this->_bindTable($sql);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid, $type), 'tid');
	}
	
	public function countThreadByFidAndType($fid, $type) {
		$sql = 'SELECT COUNT(*) AS cnt FROM %s WHERE fid=? AND topic_type=? AND disabled=0';
		$sql = $this->_bindTable($sql);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($fid, $type));
	}
	
	public function countThreadByUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) AS sum FROM %s WHERE created_userid=? AND disabled=0');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}

	public function getThreadByUid($uid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? AND disabled=0 ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'tid');
	}
	
	public function getThreadsByFidAndUids($fid, $uids, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE fid=? AND created_userid IN %s AND disabled=0 %s', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'tid');
	}
	
	public function addThread($fields) {
		return $this->_add($fields);
	}

	public function updateThread($tid, $fields, $increaseFields = array(), $bitFields = array()) {
		return $this->_update($tid, $fields, $increaseFields, $bitFields);
	}

	public function batchUpdateThread($tids, $fields, $increaseFields = array(), $bitFields = array()) {
		return $this->_batchUpdate($tids, $fields, $increaseFields, $bitFields);
	}

	public function revertTopic($tids) {
		$sql = $this->_bindSql('UPDATE %s SET disabled=ischeck^1 WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
		$result = $this->getConnection()->execute($sql);
		PwSimpleHook::getInstance('PwThreadsDao_revertTopic')->runDo($tids);
		return $result;
	}
	
	public function deleteThread($tid) {
		return $this->_delete($tid);
	}

	public function batchDeleteThread($tids) {
		return $this->_batchDelete($tids);
	}
}