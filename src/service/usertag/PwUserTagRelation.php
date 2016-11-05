<?php
/**
 * 个人标签关系
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserTagRelation.php 10043 2012-05-16 04:51:07Z jinlong.panjl $
 * @package src.service.usertag
 */
class PwUserTagRelation {
	
	/**
	 * 根据用户ID和标签ID获得用户
	 *
	 * @param int $uid
	 * @param int $tag_id
	 */
	public function getRelationByUidAndTagid($uid, $tag_id) {
		if (($uid = intval($uid)) < 1) return array();
		if (($tag_id = intval($tag_id)) < 1) return array();
		return $this->_getDao()->getRelationByUidAndTagid($uid, $tag_id);
	}
	
	/**
	 * 根据用户ID获取用户的标签
	 *
	 * @param int $uid
	 * @return PwError|array
	 */
	public function getRelationByUid($uid) {
		if (($uid = intval($uid)) < 1) return array();
		return $this->_getDao()->getRelationByUid($uid);
	}
	
	/**
	 * 根据用户ID统计用户拥有的标签
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countByUid($uid) {
		if (($uid = intval($uid)) < 1) return 0;
		return $this->_getDao()->countByUid($uid);
	}
	
	/**
	 * 根据标签ID获得该标签的相关用户记录数量
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countRelationByTagid($tag_id) {
		if (($tag_id = intval($tag_id)) < 1) return 0;
		return $this->_getDao()->countRelationByTagid($tag_id);
	}
	
	/**
	 * 根据标签ID获得该标签的关系
	 *
	 * @param int $tag_id
	 * @return array
	 */
	public function getRelationByTagid($tag_id, $limit = 20, $start = 0) {
		if (($tag_id = intval($tag_id)) < 1) return array();
		return $this->_getDao()->getRelationByTagid($tag_id, $limit, $start);
	}
	
	/**
	 * 添加关联
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @param int $time
	 * @return boolean
	 */
	public function addRelation($uid, $tag_id, $time) {
		if ($uid < 1 || $tag_id < 1) return new PwError('USER:tag.illega.format');
		return $this->_getDao()->addRelation($uid, $tag_id, $time);
	}
	
	/**
	 * 根据用户ID和标签ID删除用户和该标签的关系
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @return PwError|int
	 */
	public function deleteRelation($uid, $tag_id) {
		if (($uid = intval($uid)) < 1) return new PwError('USER:tag.uid.require');
		if (($tag_id = intval($tag_id)) < 1) return array('USER:tag.id.require');
		return $this->_getDao()->deleteRelation($uid, $tag_id);
	}
	
	/**
	 * 根据
	 *
	 * @param int $uid
	 * @param array $tag_ids
	 */
	public function batchDeleteRelation($uid, $tag_ids) {
		if (empty($tag_ids)) return false;
		PwSimpleHook::getInstance('PwUserTagRelation_batchDeleteRelation')->runDo($tag_ids, $this);
		return $this->_getDao()->batchDeleteRelation($tag_ids);
	}
	
	/**
	 * 根据用户ID删除用户标签关系
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteRelationByUid($uid) {
		if (($uid = intval($uid)) < 1) return new PwError('USER:tag.uid.require');
		PwSimpleHook::getInstance('PwUserTagRelation_deleteRelationByUid')->runDo($uid, $this);
		return $this->_getDao()->deleteRelationByUid($uid);
	}
	
	/**
	 * 批量删除用户列表中用户的关系
	 *
	 * @param array $uids
	 * @return boolean|boolean
	 */
	public function batchDeleteRelationByUids($uids) {
		if (empty($uids)) return false;
		PwSimpleHook::getInstance('PwUserTagRelation_batchDeleteRelationByUids')->runDo($uids, $this);
		return $this->_getDao()->batchDeleteRelationByUids($uids);
	}
	
	/**
	 * 获取个人标签关系Dao
	 *
	 * @return PwUserTagRelationDao
	 */
	private function _getDao() {
		return Wekit::loadDao('usertag.dao.PwUserTagRelationDao');
	}
}