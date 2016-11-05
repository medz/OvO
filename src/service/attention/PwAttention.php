<?php

/**
 * 用户关注数据服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttention.php 16021 2012-08-17 07:18:15Z jinlong.panjl $
 * @package src.service.user
 */

class PwAttention {
	
	/**
	 * 用户(A)是否已关注用户(B)
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return bool
	 */
	public function isFollowed($uid, $touid) {
		if (!$uid || !$touid) return false;
		$result = $this->_getDao()->get($uid, $touid);
		return !empty($result);
	}
	
	/**
	 * 获取用户的粉丝
	 *
	 * @param int $uid 用户id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getFans($uid, $limit = 20, $offset = 0) {
		if (!$uid) return array();
		return $this->_getDao()->getFans($uid, $limit, $offset);
	}
	
	/**
	 * 获取用户(A)指定id的粉丝数据
	 *
	 * @param int $uid 用户A
	 * @param array $touids
	 * @return array
	 */
	public function fetchFans($uid, $touids) {
		if (!$uid || !$touids || !is_array($touids)) return array();
		return $this->_getDao()->fetchFans($uid, $touids);
	}
	
	/**
	 * 分页获取用户uids中的粉丝
	 *
	 * @param array $uids
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function fetchFansByUids($uids, $limit = 20, $offset = 0) {
		if (!$uids || !is_array($uids)) return array();
		return $this->_getDao()->fetchFansByUids($uids, $limit, $offset);
	}
	
	/**
	 * 获取用户关注的人
	 *
	 * @param int $uid 用户id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getFollows($uid, $limit = 20, $offset = 0){
		if (!$uid) return array();
		return $this->_getDao()->getFollows($uid, $limit, $offset);
	}
	
	/**
	 * 获取用户(A)指定id的关注数据
	 *
	 * @param int $uid 用户A
	 * @param array $touids
	 * @return array
	 */
	public function fetchFollows($uid, $touids) {
		if (!$uid || !$touids || !is_array($touids)) return array();
		return $this->_getDao()->fetchFollows($uid, $touids);
	}

	/**
	 * 统计用户(A)关注的人中又关注了用户(B)的用户个数
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return int
	 */
	public function countFollowToFollow($uid, $touid) {
		if (!$uid || !$touid) return 0;
		return $this->_getDao()->countFollowToFollow($uid, $touid);
	}

	/**
	 * 获取用户(A)关注的人中又关注了用户(B)的用户
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @param int $limit
	 * @return array
	 */
	public function getFollowToFollow($uid, $touid, $limit = 3) {
		if (!$uid || !$touid) return array();
		return $this->_getDao()->getFollowToFollow($uid, $touid, $limit);
	}
	
	/**
	 * 用户(A)关注了用户(B)
	 * 注：本接口只是单纯的在数据层上增加一条关注数据，如果涉及完整业务，请使用接口 PwAttentionService.addFollow
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return bool| object PwError()
	 */
	public function addFollow($uid, $touid) {
		if (!$uid || !$touid) return new PwError('USER:attention.add.fail');
		if ($uid == $touid) return new PwError('USER:attention.add.self');
		if ($this->isFollowed($uid, $touid)) return new PwError('USER:attention.add.isFollowed');
		return $this->_getDao()->add(array(
			'uid' => $uid,
			'touid' => $touid,
			'created_time' => Pw::getTime()
		));
	}
	
	/**
	 * 用户(A)取消了对用户(B)关注
	 * 注：本接口只是单纯的在数据层上删除一条关注数据，如果涉及完整业务，请使用接口 PwAttentionService.deleteFollow
	 *
	 * @param int $uid 用户A
	 * @param int $touid 用户B
	 * @return bool| object PwError()
	 */
	public function deleteFollow($uid, $touid) {
		if (!$uid || !$touid) return false;
		if (!$this->isFollowed($uid, $touid)) return new PwError('USER:attention.del.fail');
		return $this->_getDao()->delete($uid, $touid);
	}

	public function getFriendsByUid($uid){
		$uid = intval($uid);
		if ($uid < 1) return false;
		return $this->_getDao()->getFriendsByUid($uid);
	}
	
	public function fetchFriendsByUids($uids){
		if (!$uids || !is_array($uids)) return array();
		return $this->_getDao()->fetchFriendsByUids($uids);
	}
	
	/**
	 * PwAttentionDao
	 * 
	 * @return PwAttentionDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('attention.dao.PwAttentionDao');
	}
}