<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseMapDbCache');

/**
 * 版块缓存数据接口
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwForumDbCache.php 21318 2012-12-04 09:24:09Z jieyin $
 * @package src.service.user
 */
class PwForumDbCache extends PwBaseMapDbCache {
	
	protected $keys = array(
		'forum' => array('forum_%s', array('fid'), PwCache::USE_DBCACHE, 'forum', 0, array('forum.dao.PwForumDao', 'getForum')),
		'forumstatistics' => array('forumstatistics_%s', array('fid'), PwCache::USE_DBCACHE, 'forum', 0, array('forum.dao.PwForumStatisticsDao', 'getForum')),
		'forumextra' => array('forumextra_%s', array('fid'), PwCache::USE_DBCACHE, 'forum', 0, array('forum.dao.PwForumExtraDao', 'getForum')),
	);

	public function getKeysByFid($fid) {
		$keys = array();
		if ($this->index & PwForum::FETCH_MAIN) $keys[] = array('forum', array($fid));
		if ($this->index & PwForum::FETCH_STATISTICS) $keys[] = array('forumstatistics', array($fid));
		if ($this->index & PwForum::FETCH_EXTRA) $keys[] = array('forumextra', array($fid));
		return $keys;
	}

	public function fetchKeysByFid($fids) {
		$keys = array();
		foreach ($fids as $fid) {
			$keys = array_merge($keys, $this->getKeysByFid($fid));
		}
		return $keys;
	}

	public function getForum($fid) {
		$data = Wekit::cache()->fetch($this->getKeysByFid($fid));
		$result = array();
		foreach ($data as $key => $value) {
			$result = array_merge($result, $value);
		}
		return $result;
	}
	
	public function fetchForum($fids) {
		$result = array();
		$data = Wekit::cache()->fetch($this->fetchKeysByFid($fids));
		foreach ($data as $key => $value) {
			list(, $fid) = explode('_', $key);
			if (isset($result[$fid])) {
				$result[$fid] = array_merge($result[$fid], $value);
			} else {
				$result[$fid] = $value;
			}
		}
		return $result;
	}

	public function updateForum($fid, $fields, $increaseFields = array()) {
		Wekit::cache()->batchDelete($this->getKeysByFid($fid));
		return $this->_getDao()->updateForum($fid, $fields, $increaseFields);
	}

	public function updateForumStatistics($fid, $subFids) {
		Wekit::cache()->batchDelete($this->getKeysByFid($fid));
		return $this->_getDao()->updateForumStatistics($fid, $subFids);
	}

	public function batchUpdateForum($fids, $fields, $increaseFields = array()) {
		Wekit::cache()->batchDelete($this->fetchKeysByFid($fids));
		return $this->_getDao()->batchUpdateForum($fids, $fields, $increaseFields);
	}

	public function deleteForum($fid) {
		Wekit::cache()->batchDelete($this->getKeysByFid($fid));
		return $this->_getDao()->deleteForum($fid);
	}
}