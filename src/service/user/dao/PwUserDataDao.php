<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户数据表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserDataDao.php 24810 2013-02-21 10:32:03Z jieyin $
 * @package src.service.user.dao
 */
class PwUserDataDao extends PwBaseDao {

	protected $_table = 'user_data';
	protected $_pk = 'uid';
	protected $_dataStruct = array('uid', 'lastvisit', 'lastlogintip', 'lastpost', 'lastactivetime', 'onlinetime', 'trypwd', 'findpwd', 'postcheck', 'message_tone', 'messages', 'notices', 'postnum', 'digest', 'todaypost', 'todayupload', 'follows', 'fans', 'likes', 'punch', 'join_forum', 'recommend_friend', 'last_credit_affect_log', 'medal_ids', 'credit1', 'credit2', 'credit3', 'credit4', 'credit5', 'credit6', 'credit7', 'credit8');
	protected $_defaultBaseInstance = 'user.dao.PwUserDefaultDao';
	
	/**
	 * 根据用户ID获得用户的数据
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
	 * 根据用户ID列表获取ID
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
	 * 根据用户名列表批量获取用户信息
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
	 * 插入用户数据
	 *
	 * @param array $fields 用户数据
	 * @return int
	 */
	public function addUser($fields) {
		if (!$this->getBaseInstance()->addUser($fields)) return false;
		$this->_add($fields, false);
		return true;
	}
	
	/** 
	 * 根据用户ID更新用户数据
	 *
	 * @param int $uid 用户ID
	 * @param array $fields 用户数据
	 * @return boolean|int
	 */
	public function editUser($uid, $fields, $increaseFields = array(), $bitFields = array()) {
		$result = $this->getBaseInstance()->editUser($uid, $fields, $increaseFields, $bitFields);
		$this->_update($uid, $fields, $increaseFields);
		return $result;
	}
	
	/** 
	 * 删除用户数据
	 *
	 * @param int $uid  用户ID
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
	
	/**
	 * 获得数据表结构
	 *
	 * @return array
	 */
	public function getDataStruct() {
		static $struct = array();
		if (!$struct) {
			$sql = $this->_bindTable('SHOW COLUMNS FROM %s');
			$tbFields = $this->getConnection()->createStatement($sql)->queryAll(array(), 'Field');
			$struct = array_keys($tbFields);
		}
		return $struct;
	}
}