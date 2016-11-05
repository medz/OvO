<?php

/**
 * 用户关注分类
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttentionType.php 19348 2012-10-13 03:52:48Z jinlong.panjl $
 * @package src.service.attention
 */

class PwAttentionType {
	
	public function getType($id) {
		if (empty($id)) return array();
		return $this->_getDao()->getType($id);
	}

	/**
	 * 获取用户拥有的分类信息
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getTypeByUid($uid) {
		return $this->_getDao()->getTypeByUid($uid);
	}
	
	/**
	 * 添加一个分类
	 *
	 * @param int $uid
	 * @param string $name
	 * @return bool|object
	 */
	public function addType($uid, $name) {
		if (empty($name)) return new PwError('USER:attention.type.name.empty');
		if (Pw::strlen($name)> 10) return new PwError('USER:attention.type.name.length.over');
		return $this->_getDao()->addType(array(
			'uid' => $uid, 'name' => $name
		));
	}
	
	/**
	 * 修改分类名称
	 *
	 * @param int $id
	 * @param string $name
	 * @return bool|object
	 */
	public function editType($id, $name) {
		if (empty($name)) return new PwError('USER:attention.type.name.empty');
		if (Pw::strlen($name)> 10) return new PwError('USER:attention.type.name.length.over');
		return $this->_getDao()->editType($id, array('name' => $name));
	}

	/**
	 * 删除某个分类
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteType($id) {
		if (empty($id) || $id < 0) return false;
		$this->_getDao()->deleteType($id);
		$this->deleteUserTypeByType($id);
		return true;
	}

	/*********** 以上是关注分组接口 **************\
	 *********************************************
	\*********** 以下是用户分组接口 **************/

	/**
	 * 获取用户所属的分组信息
	 *
	 * @param int $uid
	 * @param array $toUids
	 * @return array
	 */
	public function getUserType($uid, $touids) {
		if (empty($uid) || empty($touids) || !is_array($touids)) return array();
		return $this->_getRelationDao()->getTypeByUidAndTouids($uid, $touids);
	}

	public function countUserType($uid) {
		return $this->_getRelationDao()->count($uid);
	}
	
	/**
	 * 获取用户(A)指定分类的关注用户
	 *
	 * @param int $uid 用户(A)
	 * @param int $typeid 分类id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getUserByType($uid, $typeid, $limit = 20, $offset = 0) {
		if (empty($uid) || empty($typeid)) return array();
		return $this->_getRelationDao()->getUserByType($uid, $typeid, $limit, $offset);
	}
	
	/**
	 * 保存用户多个分类
	 *
	 * @param int $uid
	 * @param int $touid
	 * @param int $typeid
	 * @return bool
	 */
	public function addUserType($uid, $touid, $typeid) {
		if (!$uid || !$touid) return false;
		return $this->_getRelationDao()->addUserType($uid, $touid, $typeid);
	}
	
	/**
	 * 删除用户多个分类
	 *
	 * @param int $uid
	 * @param int $touid
	 * @param int $typeids
	 * @return bool
	 */
	public function deleteByUidAndTouidAndType($uid, $touid, $typeid) {
		if (!$uid || !$touid || !$typeid) return false;
		return $this->_getRelationDao()->deleteByUidAndTouidAndType($uid, $touid, $typeid);
	}
	
	/**
	 * 保存用户分类
	 *
	 * @param int $uid
	 * @param int $touid
	 * @param array $typeids
	 * @return bool
	 */
	public function saveUserType($uid, $touid, $typeids) {
		if (!$uid || !$touid || !is_array($typeids)) return false;
		$this->deleteUserType($uid, $touid);
		if ($typeids) {
			return $this->_getRelationDao()->batchAdd($uid, $touid, $typeids);
		}
		return true;
	}
	
	/**
	 * 删除用户的分类
	 *
	 * @param int $uid
	 * @param int $touid
	 * @return bool
	 */
	public function deleteUserType($uid, $touid) {
		if (!$uid || !$touid) return false;
		return $this->_getRelationDao()->deleteByUidAndTouid($uid, $touid);
	}
	
	/**
	 * 删除某个分组所有的用户关系
	 */
	public function deleteUserTypeByType($typeid) {
		if (empty($typeid)) return false;
		return $this->_getRelationDao()->deleteByType($typeid);
	}
	
	/**
	 * @return PwAttentionTypeDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('attention.dao.PwAttentionTypeDao');
	}
	
	/**
	 * @return PwAttentionRelationDao
	 */
	protected function _getRelationDao() {
		return Wekit::loadDao('attention.dao.PwAttentionRelationDao');
	}
}