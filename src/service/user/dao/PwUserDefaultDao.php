<?php

/**
 * UserDao层装饰 缺省的装饰者
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserDefaultDao.php 21135 2012-11-29 02:10:03Z jieyin $
 * @package src.service.user.dao
 */
class PwUserDefaultDao extends PwBaseDao {

	/**
	 * 根据用户ID获得用户时间
	 *
	 * @param int $uid 用户ID
	 * @return array
	 */
	public function getUserByUid($uid) {
		return array('uid' => $uid);
	}

	/**
	 * 根据用户名获取用户信息
	 *
	 * @param string $username
	 * @return array
	 */
	public function getUserByName($username) {
		$result = $this->_getDao()->getUserByName($username);
		return $result ? array('uid' => $result['uid']) : array();
	}

	/**
	 * 根据用户email获取用户信息
	 *
	 * @param string $email
	 * @return array
	 */
	public function getUserByEmail($email) {
		$result = $this->_getDao()->getUserByEmail($email);
		return $result ? array('uid' => $result['uid']) : array();
	}

	/**
	 * 根据用户ID列表批量获取用户信息
	 *
	 * @param unknown_type $uids
	 * @return multitype:multitype: 
	 */
	public function fetchUserByUid($uids) {
		$info = array();
		foreach ($uids as $value) {
			$info[$value] = array();
		}
		return $info;
	}

	/**
	 * 根据用户名列表批量获取用户信息
	 *
	 * @param array $usernames
	 * @return array  
	 */
	public function fetchUserByName($usernames) {
		$data = $this->_getDao()->fetchUserByName($usernames);
		$result = array();
		foreach ($data as $key => $value) {
			$result[$key] = array('uid' => $key);
		}
		return $result;
	}

	/**
	 * 添加用户
	 *
	 * @param array $fields 待添加的用户信息
	 * @return boolean
	 */
	public function addUser($fields) {
		return false;
	}

	/**
	 * 更新用户信息
	 *
	 * @param int $uid 用户ID
	 * @param array $fields 用户信息
	 * @return boolean
	 */
	public function editUser($uid, $fields, $increaseFields = array(), $bitFields = array()) {
		return false;
	}

	/**
	 * 删除用户信息
	 *
	 * @param int $uids
	 * @return boolean
	 */
	public function deleteUser($uids) {
		return false;
	}

	/** 
	 * 批量删除用户信息
	 *
	 * @param array $uids 用户ID
	 * @return boolean
	 */
	public function batchDeleteUser($uids) {
		return false;
	}

	/**
	 * 获取用户基本信息dao
	 * 
	 * @return PwUserDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('user.dao.PwUserDao');
	}
}