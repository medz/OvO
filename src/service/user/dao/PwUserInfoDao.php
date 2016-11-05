<?php

/**
 * 用户扩展信息表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserInfoDao.php 21135 2012-11-29 02:10:03Z jieyin $
 * @package src.service.user.dao
 */
class PwUserInfoDao extends PwBaseDao {

	protected $_table = 'user_info';
	protected $_pk = 'uid';
	protected $_dataStruct = array('uid', 'gender','byear', 'bmonth', 'bday', 'location', 'location_text', 'hometown', 'hometown_text', 'homepage', 'qq', 'msn', 'aliww', 'mobile', 'alipay', 'bbs_sign', 'profile', 'regreason', 'telphone', 'address', 'zipcode', 'secret');
	protected $_defaultBaseInstance = 'user.dao.PwUserDefaultDao';
	
	/** 
	 * 根据用户ID获得用户信息
	 *
	 * @param int $uid 用户ID
	 * @return array
	 */
	public function getUserByUid($uid) {
		$info = $this->getBaseInstance()->getUserByUid($uid);
		return array_merge($info, $this->_get($uid));
	}
	
	/**
	 * 根据用户名获得用户信息
	 *
	 * @param string $username
	 * @return array
	 */
	public function getUserByName($username) {
		if (!$info = $this->getBaseInstance()->getUserByName($username)) {
			return array();
		}
		return array_merge($info, $this->_get($info['uid']));
	}

	/**
	 * 根据用户email获得用户信息
	 *
	 * @param string $email
	 * @return array
	 */
	public function getUserByEmail($email) {
		if (!$info = $this->getBaseInstance()->getUserByEmail($email)) {
			return array();
		}
		return array_merge($info, $this->_get($info['uid']));
	}

	/**
	 * 根据用户ID列表批量获得用户信息
	 *
	 * @param array $uids
	 * @return array
	 */
	public function fetchUserByUid($uids) {
		$info = $this->getBaseInstance()->fetchUserByUid($uids);
		if ($info) $info = $this->_margeArray($info, $this->_fetch(array_keys($info), 'uid'));
		return $info;
	}

	/**
	 * 根据用户名列表批量获得用户信息
	 *
	 * @param array $usernames
	 * @return array
	 */
	public function fetchUserByName($usernames) {
		$info = $this->getBaseInstance()->fetchUserByName($usernames);
		if ($info) $info = $this->_margeArray($info, $this->_fetch(array_keys($info), 'uid'));
		return $info;
	}
	
	/** 
	 * 添加用户资料
	 *
	 * @param array $fields 用户数据信息
	 * @return int
	 */
	public function addUser($fields) {
		if (!$this->getBaseInstance()->addUser($fields)) return false;
		$this->_add($fields, false);
		return true;
	}
	
	/** 
	 * 更新用户信息
	 *
	 * @param int $uid 用户ID
	 * @param array $fields 用户信息数据
	 * @return int|boolean
	 */
	public function editUser($uid, $fields, $increaseFields = array(), $bitFields = array()) {
		$result = $this->getBaseInstance()->editUser($uid, $fields, $increaseFields, $bitFields);
		$this->_update($uid, $fields, $increaseFields);
		return $result;
	}
	
	/** 
	 * 删除用户数据
	 *
	 * @param int $uid 用户ID
	 * @return int
	 */
	public function deleteUser($uid) {
		$result = $this->getBaseInstance()->deleteUser($uid);
		$this->_delete($uid);
		return $result;
	}
	
	/** 
	 * 批量删除用户信息
	 *
	 * @param array $uids 用户ID
	 * @return int
	 */
	public function batchDeleteUser($uids) {
		$result = $this->getBaseInstance()->batchDeleteUser($uids);
		$this->_batchDelete($uids);
		return $result;
	}
}