<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseMapDbCache');

/**
 * 用户缓存数据接口
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserDbCache.php 21135 2012-11-29 02:10:03Z jieyin $
 * @package src.service.user
 */
class PwUserDbCache extends PwBaseMapDbCache {
	
	protected $keys = array(
		'user' => array('user_%s', array('uid'), PwCache::USE_DBCACHE, 'user', 0, array('user.dao.PwUserDao', 'getUserByUid')),
		'userdata' => array('userdata_%s', array('uid'), PwCache::USE_DBCACHE, 'user', 0, array('user.dao.PwUserDataDao', 'getUserByUid')),
		'userinfo' => array('userinfo_%s', array('uid'), PwCache::USE_DBCACHE, 'user', 0, array('user.dao.PwUserInfoDao', 'getUserByUid')),
	);

	public function getKeysByUid($uid) {
		$keys = array();
		if ($this->index & PwUser::FETCH_MAIN) $keys[] = array('user', array($uid));
		if ($this->index & PwUser::FETCH_DATA) $keys[] = array('userdata', array($uid));
		if ($this->index & PwUser::FETCH_INFO) $keys[] = array('userinfo', array($uid));
		return $keys;
	}

	public function fetchKeysByUid($uids) {
		$keys = array();
		foreach ($uids as $uid) {
			$keys = array_merge($keys, $this->getKeysByUid($uid));
		}
		return $keys;
	}

	public function getUserByUid($uid) {
		$data = Wekit::cache()->fetch($this->getKeysByUid($uid));
		$result = array();
		foreach ($data as $key => $value) {
			$result = array_merge($result, $value);
		}
		return $result;
	}
	
	public function fetchUserByUid($uids) {
		$result = array();
		$data = Wekit::cache()->fetch($this->fetchKeysByUid($uids));
		foreach ($data as $key => $value) {
			list(, $uid) = explode('_', $key);
			if (isset($result[$uid])) {
				$result[$uid] = array_merge($result[$uid], $value);
			} else {
				$result[$uid] = $value;
			}
		}
		return $result;
	}

	public function editUser($uid, $fields, $increaseFields = array(), $bitFields = array()) {
		Wekit::cache()->batchDelete($this->getKeysByUid($uid));
		return $this->_getDao()->editUser($uid, $fields, $increaseFields, $bitFields);
	}

	public function deleteUser($uid) {
		Wekit::cache()->batchDelete($this->getKeysByUid($uid));
		return $this->_getDao()->deleteUser($uid);
	}

	public function batchDeleteUser($uids) {
		Wekit::cache()->batchDelete($this->fetchKeysByUid($uids));
		return $this->_getDao()->batchDeleteUser($uids);
	}
}