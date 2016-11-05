<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子内容dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadsContentDao.php 20446 2012-10-30 03:13:45Z jieyin $
 * @package forum
 */

class PwThreadsContentDao extends PwBaseDao {
	
	protected $_table = 'bbs_threads_content';
	protected $_pk = 'tid';
	protected $_dataStruct = array('tid', 'useubb', 'aids', 'content', 'sell_count', 'reminds', 'word_version', 'tags', 'ipfrom', 'manage_remind');
	protected $_defaultBaseInstance = 'forum.dao.PwThreadsBaseDao';
	
	public function getThread($tid) {
		if (!$result = $this->getBaseInstance()->getThread($tid)) {
			return $result;
		}
		return array_merge($result, $this->_get($tid));
	}
	
	public function fetchThread($tids) {
		if (!$result = $this->getBaseInstance()->fetchThread($tids)) {
			return $result;
		}
		return $this->_margeArray($result, $this->_fetch($tids, 'tid'));
	}

	public function getThreadByFid($fid, $limit, $offset) {
		if (!$result = $this->getBaseInstance()->getThreadByFid($fid, $limit, $offset)) {
			return $result;
		}
		return $this->_margeArray($result, $this->_fetch(array_keys($result), 'tid'));
	}

	public function getThreadByUid($uid, $limit, $offset) {
		if (!$result = $this->getBaseInstance()->getThreadByUid($uid, $limit, $offset)) {
			return $result;
		}
		return $this->_margeArray($result, $this->_fetch(array_keys($result), 'tid'));
	}

	public function getThreadsByFidAndUids($fid, $uids, $limit, $offset) {
		if (!$result = $this->getBaseInstance()->getThreadsByFidAndUids($fid, $uids, $limit, $offset)) {
			return $result;
		}
		return $this->_margeArray($result, $this->_fetch(array_keys($result), 'tid'));
	}
	
	public function addThread($fields) {
		if (!$tid = $this->getBaseInstance()->addThread($fields)) {
			return false;
		}
		$fields['tid'] = $tid;
		$this->_add($fields, false);
		return $tid;
	}

	public function updateThread($tid, $fields, $increaseFields = array(), $bitFields = array()) {
		$result = $this->getBaseInstance()->updateThread($tid, $fields, $increaseFields, $bitFields);
		$this->_update($tid, $fields, $increaseFields);
		return $result;
	}

	public function batchUpdateThread($tids, $fields, $increaseFields = array(), $bitFields = array()) {
		$result = $this->getBaseInstance()->batchUpdateThread($tids, $fields, $increaseFields, $bitFields);
		$this->_batchUpdate($tids, $fields, $increaseFields);
		return $result;
	}
	
	public function deleteThread($tid){
		$this->getBaseInstance()->deleteThread($tid);
		return $this->_delete($tid);
	}

	public function batchDeleteThread($tids) {
		$this->getBaseInstance()->batchDeleteThread($tids);
		$this->_batchDelete($tids);
		return true;
	}
}