<?php
/**
 * 用户禁言DS
 * 用户行为禁止：
 *  1：禁止用户发言
 *  2: 禁止用户使用头像
 *  4： 禁止用户使用签名
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBan.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.service.user
 */
class PwUserBan {
	const BAN_SPEAK = 1;
	const BAN_AVATAR = 2;
	const BAN_SIGN = 4;
	const BAN_ALL = 7;

	/**
	 * 获得用户所有禁止信息
	 *
	 * @param int $uid 用户ID
	 * @return array
	 */
	public function getBanInfo($uid) {
		if (0 >= ($uid = intval($uid))) return array();
		return $this->_getDao()->getBanInfo($uid);
	}
	
	/**
	 * 根据用户ID和禁止类型获得该用户的禁止信息
	 *
	 * @param int $uid 用户ID
	 * @param int $typeid 禁止类型
	 * @return array
	 */
	public function getBanInfoByTypeid($uid, $typeid = self::BAN_SIGN) {
		if (0 >= ($uid = intval($uid))) return array();
		return $this->_getDao()->getBanInfoByTypeid($uid, $typeid);
	}
	
	/**
	 * 根据禁止类型及其ID获得用户uid的禁止信息
	 *
	 * @param int $uid 用户ID
	 * @param int $typeid 禁止类型
	 * @param int $fid 版块ID
	 * @return array
	 */
	public function getBanInfoByTypeidAndFid($uid, $typeid = self::BAN_SPEAK, $fid = 0) {
		if (0 >= ($uid = intval($uid))) return array();
		return $this->_getDao()->getBanInfoByTypeidAndFid($uid, $typeid, $fid);
	}
	
	/**
	 * 根据用户ID获得用户禁止信息
	 *
	 * @param array $uids 用户ID列表
	 * @param int $typeid 禁止类型
	 * @return array
	 */
	public function fetchBanInfoByUid($uids, $typeid = self::BAN_SPEAK) {
		if (!$uids) return array();
		return $this->_getDao()->fetchBanInfoByUid($uids, $typeid);
	}
	
	/**
	 * 根据禁止ID批量获取
	 *
	 * @param array $ids
	 * @return array
	 */
	public function fetchBanInfo($ids) {
		if (!$ids) return array();
		return $this->_getDao()->fetchBanInfo($ids);
	}
	
	/** 
	 * 添加禁言用户
	 *
	 * @param PwUserBanInfoDm $dm 用户禁止DM
	 * @return boolean
	 */
	public function addBanInfo(PwUserBanInfoDm $dm) {
		if (true != ($result = $dm->beforeAdd())) return $result;
		return $this->_getDao()->addBanInfo($dm->getData());
	}
	
	/**
	 * 批量禁止用户
	 *
	 * @param array $data
	 * @return array
	 */
	public function batchAddBanInfo($data) {
		if (!$data) return false;
		return $this->_getDao()->batchAddBanInfo($data);
	}
	
	/**
	 * 根据用户ID删除用户的屏蔽信息
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteByUid($uid) {
		$uid = intval($uid);
		if ($uid < 1) return false;
		return $this->_getDao()->deleteByUid($uid);
	}
	
	/** 
	 * 根据用户ID批量删除该用户信息
	 *
	 * @param array $uids 用户ID列表
	 * @return int|boolean
	 */
	public function batchDeleteByUids($uids) {
		if (!$uids) return false;
		return $this->_getDao()->batchDeleteByUids($uids);
	}
	
	/**
	 * 根据ID列表删除禁止记录
	 *
	 * @param array $ids 禁止记录ID列表
	 * @return boolean
	 */
	public function batchDelete($ids) {
		if (!$ids) return false;
		return $this->_getDao()->batchDelete($ids);
	}
	
	/**
	 * 根据搜索条件统计总条数
	 *
	 * @param PwUserBanSo $searchVo
	 * @return int
	 */
	public function countWithCondition(PwUserBanSo $searchVo) {
		return $this->_getDao()->countByCondition($searchVo->getData());
	}
	
	/**
	 * 根据搜索条件返回检索结果列表
	 *
	 * @param PwUserBanSo $searchVo
	 * @param int $start 开始检索位置
	 * @param int $limit 返回条数
	 * @return array
	 */
	public function searchBanInfo(PwUserBanSo $searchVo, $limit = 10, $start = 0) {
		return $this->_getDao()->fetchBanInfoByCondition($searchVo->getData(), $limit, $start);
	}

	/** 
	 * 获得用户禁止DAO
	 *
	 * @return PwUserBanDao
	 */
	private function _getDao() {
		return Wekit::loadDao('user.dao.PwUserBanDao');
	}
}