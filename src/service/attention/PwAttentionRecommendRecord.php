<?php

/**
 * 可能认识的人DS
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwAttentionRecommendRecord {
	
	public function getRecommendFriend($uid,$limit = 30,$offset = 0){
		$uid = intval($uid);
		if ($uid < 1) return array();
		return $this->_getDao()->getRecommendFriend($uid, $limit, $offset);
	}
	
	public function addRecommendFriend($uid, $recommendUid, $sameUid) {
		if (!$uid || !$recommendUid || $uid == $recommendUid) return false;
		
		return $this->_getDao()->replace(array(
			'uid' => $uid,
			'recommend_uid' => $recommendUid,
			'same_uid' => $sameUid
		));
	}
	
	public function batchReplaceRecommendFriend($data) {
		if (!$data || !is_array($data)) return false;
		return $this->_getDao()->batchReplace($data);
	}
	
	/**
	 * 
	 * 删除某用户的潜在好友
	 * @param int $uid
	 * @param int $recommendUid
	 */
	public function deleteRecommendFriend($uid, $recommendUid = 0){
		$uid = intval($uid);
		$recommendUid = intval($recommendUid);
		if ($uid < 1) return false;
		if ($recommendUid) {
			return $this->_getDao()->deleteRecommendFriend($uid,$recommendUid);
		} else {
			return $this->_getDao()->deleteRecommendFriendByUid($uid);
		}
	}
	
	/**
	 * 
	 * 删除某用户的潜在好友
	 * @param int $uid
	 * @param int $sameUid
	 */
	public function deleteByUidAndSameUid($uid, $sameUid){
		$uid = intval($uid);
		$sameUid = intval($sameUid);
		if ($uid < 1 || $sameUid < 1) return false;
		return $this->_getDao()->deleteByUidAndSameUid($uid,$sameUid);
	}
	
	/**
	 *
	 * @return PwAttentionRecommendRecordDao
	 */
	private function _getDao() {
		return Wekit::loadDao('attention.dao.PwAttentionRecommendRecordDao');
	}
	
	/**
	 *
	 * @return PwAttentionDao
	 */
	private function _getAttentionDao() {
		return Wekit::loadDao('attention.dao.PwAttentionDao');
	}
}