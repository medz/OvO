<?php
/**
 * 用户注册的审核/激活DS
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserRegisterCheck.php 7687 2012-04-10 11:17:58Z xiaoxia.xuxx $
 * @package src.service.user
 */
class PwUserRegisterCheck {

	/**
	 * 根据用户的ID获得用户的状态
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getInfo($uid) {
		if (empty($uid)) return array();
		return $this->_getDao()->getInfo($uid);
	}
	
	/**
	 * 获得没有激活的用户
	 * 
	 * @return array
	 */
	public function getUnCheckedList($limit, $start) {
		return $this->_getDao()->getInfoByIfchecked(0, $limit, $start);
	}
	
	/**
	 * 统计没有激活的用户
	 * 
	 * @return int
	 */
	public function countUnChecked() {
		return $this->_getDao()->countByIfchecked(0);
	}
	
	/**
	 * 获得没有激活的用户列表
	 *
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function getUnActivedList($limit, $start) {
		return $this->_getDao()->getInfoByIfactived(0, $limit, $start);
	}
	
	/**
	 * 统计没有激活的用户
	 *
	 * @return int
	 */
	public function countUnActived() {
		return $this->_getDao()->countByIfactived(0);
	}
	
	/** 
	 * 添加用户的状态
	 *
	 * @param int $uids 用户ID
	 * @param int $ifchecked 是否已经审核
	 * @param int $ifactived 是否已经激活 
	 * @return boolean|int
	 */
	public function addInfo($uid, $ifchecked = 1, $ifactived = 1) {
		if (empty($uid)) return false;
		return $this->_getDao()->addInfo($uid, $ifchecked, $ifactived);
	}
	
	/**
	 * 审核通过用户
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function checkUser($uid) {
		if (empty($uid)) return false;
		return $this->_getDao()->updateInfo($uid, array('ifchecked' => 1));
	}
	
	/**
	 * 批量审核用户
	 *
	 * @param array $uids
	 * @return boolean
	 */
	public function batchCheckUser($uids) {
		if (empty($uids)) return false;
		return $this->_getDao()->batchUpdateInfo($uids, array('ifchecked' => 1));
	}
	
	/**
	 * 激活用户
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function activeUser($uid) {
		if (empty($uid)) return false;
		return $this->_getDao()->updateInfo($uid, array('ifactived' => 1));
	}
	
	/**
	 * 批量激活用户
	 *
	 * @param array $uids
	 * @return boolean
	 */
	public function batchActiveUser($uids) {
		if (empty($uids)) return false;
		return $this->_getDao()->batchUpdateInfo($uids, array('ifactived' => 1));
	}
	
	/**
	 * 根据用户ID删除用户的状态
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteUser($uid) {
		if (empty($uid)) return false;
		return $this->_getDao()->deleteInfo($uid);
	}
	
	/**
	 * 根据用户的ID列表删除批量删除
	 *
	 * @param array $uids
	 * @return boolean
	 */
	public function batchDeleteUser($uids) {
		if (empty($uids)) return false;
		return $this->_getDao()->batchDeleteInfo($uids);
	}
	
	/**
	 * 返回用户注册审核/激活DAO
	 *
	 * @return PwUserRegisterCheckDao
	 */
	private function _getDao() {
		return Wekit::loadDao('user.dao.PwUserRegisterCheckDao');
	}
}