<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:user.dm.PwUserInfoDm');

/**
 * 用户操作数据接口
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUser.php 24770 2013-02-20 11:22:38Z jieyin $
 * @package src.service.user
 */
class PwUser {
	
	const FETCH_MAIN = 1; //获取用户基本信息，比如帐号、邮箱
	const FETCH_DATA = 2; //获取用户数据，比如积分、发帖数等论坛统计信息
	const FETCH_INFO = 4; //获取用户基本资料
	const FETCH_ALL = 7;

	const STATUS_UNCHECK = 1;//未验证用户
	const STATUS_UNACTIVE = 2;//未激活用户
	const STATUS_BAN_AVATAR = 3;//用户被禁止头像
	const STATUS_BAN_SIGN = 4;//用户被禁止签名
	
	const STATUS_ALLOW_LOGIN_ADMIN = 5;//该用户是否后后台权限
	const STATUS_SIGN_USEUBB = 6;//该用户签名是否使用UBB
	
	/** 
	 * 根据用户ID获得用户数据信息
	 *
	 * @param int $uid 	用户ID
	 * @param int $type 获得信息的类型
	 * @return array
	 */
	public function getUserByUid($uid, $type = self::FETCH_MAIN) {
		if (empty($uid)) return array();
		return $this->_getDao($type)->getUserByUid($uid);
	}

	/** 
	 * 根据用户名字获得用户数据信息
	 *
	 * @param string $username  用户名
	 * @param int $type   		获得信息的类型
	 * @return array
	 */
	public function getUserByName($username, $type = self::FETCH_MAIN) {
		if (empty($username)) return array();
		return $this->_getDao($type)->getUserByName($username);
	}
	
	/**
	 * 通过邮箱获取用户信息
	 *
	 * @param string $email 邮箱
	 * @param int $type 用户信息类型
	 * @return array
	 */
	public function getUserByEmail($email, $type = self::FETCH_MAIN) {
		if (empty($email)) return array();
		return $this->_getDao($type)->getUserByEmail($email);
	}
	
	/** 
	 * 跟据用户ID列表获取用户列表
	 *
	 * @param array $uids 用户列表
	 * @param int $type
	 */
	public function fetchUserByUid($uids, $type = self::FETCH_MAIN) {//getUserListByUids($uids, $type = self::FETCH_MAIN) {
		if (empty($uids) || !is_array($uids)) return array();
		return $this->_getDao($type)->fetchUserByUid($uids);
	}
	
	/** 
	 * 根据用户名
	 *
	 * @param array $names 用户名列表
	 * @param int $type
	 * @return array
	 */
	public function fetchUserByName($names, $type = PwUser::FETCH_MAIN) {//getUserListByNames($names, $type = PwUser::FETCH_MAIN) {
		if (empty($names) || !is_array($names)) return array();
		return $this->_getDao($type)->fetchUserByName($names);
	}

	/** 
	 * 编辑用户信息
	 *
	 * @param PwUserInfoDm $dm 用户信息DM
	 * @param int $type 	   更新类型
	 * @return boolean|PwError
	 */
	public function editUser(PwUserInfoDm $dm, $type = self::FETCH_ALL) {
		if (true !== ($result = $dm->beforeUpdate())) return $result;
		if (is_object($dm->dm)) {
			$result = $this->_getWindid()->editDmUser($dm->dm);
			if ($result < 1) return new PwError('WINDID:code.' . $result);
		}
		$result = $this->_getDao($type)->editUser($dm->uid, $dm->getData(), $dm->getIncreaseData(), $dm->getBitData());
		PwSimpleHook::getInstance('PwUser_update')->runDo($dm);
		return true;
	}
	
	/**
	 * 更新用户积分
	 *
	 * @param object $dm
	 * @return bool
	 */
	public function updateCredit(PwCreditDm $dm) {
		if (is_null($dm->dm)) return false;
		$result = $this->_getWindid()->editDmCredit($dm->dm);
		if ($result < 1) return new PwError('WINDID:code.' . $result);
		return $this->_getDao(self::FETCH_DATA)->editUser($dm->uid, $this->_getWindid()->getUserCredit($dm->uid));
	}

	/** 
	 * 添加用户
	 *
	 * @param PwUserInfoDm $dm 用户信息DM
	 * @param int $type 	   添加表
	 * @return int|PwError
	 */
	public function addUser(PwUserInfoDm $dm) {
		if (true !== ($result = $dm->beforeAdd())) return $result;
		if (($uid = $this->_getWindid()->addDmUser($dm->dm)) < 1) {
			return new PwError('WINDID:code.' . $uid);
		}
		$dm->setUid($uid);
		$this->_getDao(self::FETCH_ALL)->addUser($dm->getSetData());
		PwSimpleHook::getInstance('PwUser_add')->runDo($dm);
		return $uid;
	}
	
	/**
	 * 激活用户
	 *
	 * @param int $uid
	 * @return bool
	 */
	public function activeUser($uid) {
		if (!$data = $this->_getWindid()->getUser($uid, 1, PwUser::FETCH_ALL)) {
			return false;
		}
		$data['password'] = md5(WindUtility::generateRandStr(16));
		return $this->_getDao(self::FETCH_ALL)->addUser($data);
	}
	
	public function synEditUser($uid, $changepwd = 0) {
		if (!$data = $this->_getWindid()->getUser($uid, 1, PwUser::FETCH_ALL)) {
			return false;
		}
		$changepwd && $data['password'] = md5(WindUtility::generateRandStr(16));
		return $this->_getDao(self::FETCH_ALL)->editUser($uid, $data);
	}

	/** 
	 * 删除用户信息
	 *
	 * @param int $uid 用户ID
	 * @return boolean
	 */
	public function deleteUser($uid) {
		if (0 >= ($uid = intval($uid))) return new PwError('USER:illegal.id');
		$this->_getDao(self::FETCH_ALL)->deleteUser($uid);
		$this->_getWindid()->deleteUser($uid);
		PwSimpleHook::getInstance('PwUser_delete')->runDo($uid);
		return true;
	}
	
	/** 
	 * 根据用户ID列表批量删除用户信息
	 *
	 * @param array $uids 用户ID列表
	 * @return boolean
	 */
	public function batchDeleteUserByUid($uids) {
		if (empty($uids)) return false;
		$uids = (array)$uids;
		$this->_getDao(self::FETCH_ALL)->batchDeleteUser($uids);
		$this->_getWindid()->batchDeleteUser($uids);
		PwSimpleHook::getInstance('PwUser_batchDelete')->runDo($uids);
		return true;
	}
	
	/** 
	 * 获得用户中心对像
	 * 
	 * @return PwBaseDao
	 */
	protected function _getDao($type = self::FETCH_MAIN) {
		$daoMap = array();
		$daoMap[self::FETCH_MAIN] = 'user.dao.PwUserDao';
		$daoMap[self::FETCH_DATA] = 'user.dao.PwUserDataDao';
		$daoMap[self::FETCH_INFO] = 'user.dao.PwUserInfoDao';
		return Wekit::loadDaoFromMap($type, $daoMap, 'PwUser');
	}
	
	/** 
	 * 获得windidDS
	 *
	 * @return WindidUser
	 */
	protected function _getWindid() {
		return Pw::windid('user');
	}
}