<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseMapDbCache');

/**
 * 帖子缓存数据接口
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadDbCache.php 22201 2012-12-19 15:38:16Z jieyin $
 * @package src.service.user
 */
class PwThreadDbCache extends PwBaseMapDbCache {
	
	protected $keys = array(
		'thread' => array('thread_%s', array('tid'), PwCache::USE_DBCACHE, 'forum', 0, array('forum.dao.PwThreadsDao', 'getThread')),
		'threadcontent' => array('threadcontent_%s', array('tid'), PwCache::USE_DBCACHE, 'forum', 0, array('forum.dao.PwThreadsContentDao', 'getThread')),
		'thread_list' => array('thread_list_%s_%s_%s_%s', array('fver', 'fid', 'limit', 'offset'), PwCache::USE_DBCACHE, 'forum', 0),
		'thread_fver' => array('thread_fver_%s', array('fid'), PwCache::USE_DBCACHE, 'forum', 0, 0),
	);

	public function getKeysByTid($tid) {
		$keys = array();
		if ($this->index & PwThread::FETCH_MAIN) $keys[] = array('thread', array($tid));
		if ($this->index & PwThread::FETCH_CONTENT) $keys[] = array('threadcontent', array($tid));
		return $keys;
	}

	public function fetchKeysByTid($tids) {
		$keys = array();
		foreach ($tids as $tid) {
			$keys = array_merge($keys, $this->getKeysByTid($tid));
		}
		return $keys;
	}

	public function getThread($tid) {
		$data = Wekit::cache()->fetch($this->getKeysByTid($tid));
		$result = array();
		foreach ($data as $key => $value) {
			$result = array_merge($result, $value);
		}
		return $result;
	}
	
	public function fetchThread($tids) {
		$result = array();
		$data = Wekit::cache()->fetch($this->fetchKeysByTid($tids));
		foreach ($data as $key => $value) {
			list(, $tid) = explode('_', $key);
			if (isset($result[$tid])) {
				$result[$tid] = array_merge($result[$tid], $value);
			} else {
				$result[$tid] = $value;
			}
		}
		return $result;
	}

	public function getThreadByFid($fid, $limit, $offset) {
		$fver = Wekit::cache()->get('thread_fver', array($fid));
		$data = Wekit::cache()->get('thread_list', array($fver, $fid, $limit, $offset));
		if ($data === false) {
			$result = $this->_getDao()->getThreadByFid($fid, $limit, $offset);
			Wekit::cache()->set('thread_list', array_keys($result), array($fver, $fid, $limit, $offset));
		} else {
			$result = $this->fetchThread($data);
		}
		return $result;
	}

	public function addThread($fields) {
		if ($fields['fid'] && (!isset($fields['disabled']) || $fields['disabled'] == 0)) {
			$this->clearThreadListCache($fields['fid']);
		}
		return $this->_getDao()->addThread($fields);
	}

	public function updateThread($tid, $fields, $increaseFields = array(), $bitFields = array()) {
		if (isset($fields['disabled']) || isset($fields['lastpost_time']) || isset($fields['fid'])) {
			$this->updateThreadList($tid, isset($fields['fid']) ? $fields['fid'] : 0);
		}
		Wekit::cache()->batchDelete($this->getKeysByTid($tid));
		return $this->_getDao()->updateThread($tid, $fields, $increaseFields, $bitFields);
	}

	public function batchUpdateThread($tids, $fields, $increaseFields = array(), $bitFields = array()) {
		if (isset($fields['disabled']) || isset($fields['lastpost_time']) || isset($fields['fid'])) {
			$this->batchUpdateThreadList($tids, isset($fields['fid']) ? $fields['fid'] : 0);
		}
		Wekit::cache()->batchDelete($this->fetchKeysByTid($tids));
		return $this->_getDao()->batchUpdateThread($tids, $fields, $increaseFields, $bitFields);
	}

	public function deleteThread($tid) {
		$this->updateThreadList($tid);
		Wekit::cache()->batchDelete($this->getKeysByTid($tid));
		return $this->_getDao()->deleteThread($tid);
	}

	public function batchDeleteThread($tids) {
		$this->batchUpdateThreadList($tids);
		Wekit::cache()->batchDelete($this->fetchKeysByTid($tids));
		return $this->_getDao()->batchDeleteThread($tids);
	}

	public function revertTopic($tids) {
		$this->batchUpdateThreadList($tids);
		Wekit::cache()->batchDelete($this->fetchKeysByTid($tids));
		return $this->_getDao()->revertTopic($tids);
	}
	
	/**
	 * 清除一个版块的列表缓存缓存
	 *
	 * @param int $fid
	 */
	public function clearThreadListCache($fid) {
		Wekit::cache()->increment('thread_fver', array($fid));
	}
	
	/**
	 * 更新一个帖子，清除所属版块的列表缓存
	 *
	 * @param int $tid
	 * @param int $fid
	 */
	public function updateThreadList($tid, $fid = 0) {
		$thread = $this->getThread($tid);
		$this->clearThreadListCache($thread['fid']);
		if ($fid && $fid != $thread['fid']) {
			$this->clearThreadListCache($fid);
		}
	}
	
	/**
	 * 更新多个帖子时，清除所属版块的列表缓存
	 *
	 * @param array $tids
	 * @param int $fid
	 */
	public function batchUpdateThreadList($tids, $fid = 0) {
		$threads = $this->fetchThread($tids);
		$fids = array();
		foreach ($threads as $thread) {
			$fids[] = $thread['fid'];
		}
		if ($fid) {
			$fids[] = $fid;
		}
		$fids = array_unique($fids);
		foreach ($fids as $_fid) {
			$this->clearThreadListCache($_fid);
		}
	}
}