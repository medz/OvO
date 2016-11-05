<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户组提升、总积分计算相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserGroupsService.php 24736 2013-02-19 09:24:40Z jieyin $
 * @package src.service.user.srv
 */

class PwUserGroupsService {
	
	protected $_nkey = array('allow_sign', 'sign_max_height', 'sign_ubb', 'sign_ubb_img');

	/**
	 * 计算用户综合积分
	 * 
	 * @param array $strategy 积分计算策略
	 * @param array $user 用户信息
	 * @return int
	 */
	public function calculateCredit($strategy, $user) {
		$credit = 0;
		if (is_array($strategy) && $strategy) {
			foreach ($strategy as $key => $value) {
				if (!$value || !$user[$key]) continue;
				if ($key == 'onlinetime') $user[$key] = (int)($user[$key]/3600);
				$credit += $user[$key] * $value;
			}
		}
		return (int)$credit;
	}
	
	/**
	 * 获取用户综合积分
	 * 
	 * @param array $user 用户信息
	 * @return int
	 */
	public function getCredit($user) {
		if (!$strategy = Wekit::C('site', 'upgradestrategy')) {
			return 0;
		}
		return $this->calculateCredit($strategy, $user);
	}

	/**
	 * 计算用户所在升级组的ID
	 *
	 * @param int $credit 综合积分
	 * @param string $type member/vip
	 * @return int
	 */
	public function calculateLevel($credit) {
		$_cache = Wekit::cache()->get('level');
		$lneed = $_cache['lneed'];
		$gid = 0;
		arsort($lneed);
		reset($lneed);
		foreach ($lneed as $key => $lowneed) {
			$gid = $key;
			if ($credit >= $lowneed) break;
		}
		return $gid;
	}

	/**
	 * 通过监测用户数据更新字段，自动更新用户组 (hook)
	 *
	 * @param int $uid
	 * @param array $fields
	 * @param array $increaseFields
	 * @return bool
	 */
	public function updateLevel($uid, $fields, $increaseFields) {
		$strategy = Wekit::C('site', 'upgradestrategy');
		if (!is_array($strategy)) return false;
		$map = array_keys($strategy);
		if (!array_intersect($map, array_keys($fields)) && !array_intersect($map, array_keys($increaseFields))) {
			return false;
		}
		if (!$user = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_MAIN | PwUser::FETCH_DATA)) {
			return false;
		}
		
		$credit = $this->calculateCredit($strategy, $user);
		$memberid = $this->calculateLevel($credit);
		if ($memberid != $user['memberid']) {
			Wind::import('SRV:user.dm.PwUserInfoDm');
			$dm = new PwUserInfoDm($uid);
			$dm->setMemberid($memberid);
			return $this->_getUserDs()->editUser($dm, PwUser::FETCH_MAIN);
		}
		return false;
	}
	
	/**
	 * 通过监测数据更新字段，自动更新用户组缓存 (hook)
	 */
	public function updateGroupCacheByHook($gids) {
		$this->updateGroupCache($gids);
		$this->updateLevelCache();
	}
	
	/**
	 * 通过监测数据更新字段，自动删除用户组缓存 (hook)
	 */
	public function deleteGroupCacheByHook($gid) {
		Wekit::cache()->delete('group', array($gid));
		$this->updateLevelCache();
	}

	/**
	 * 通过监测数据更新字段，自动更新权限缓存 (hook)
	 */
	public function updatePermissionCacheByHook(PwUserPermissionDm $dm) {
		$gkey = array_keys($dm->getPermission());
		if (empty($gkey)) return;
		$this->updateGroupCache(array($dm->getGid()));
		$this->updateGroupRightCache($gkey);
	}
	
	/**
	 * 更新全局用户组等级名称缓存
	 *
	 * @return void
	 */
	public function updateLevelCache() {
		Wekit::cache()->set('level', $this->getLevelCacheValue());
	}
	
	/**
	 * 获取全局用户组等级名称缓存
	 *
	 * @return array
	 */
	public function getLevelCacheValue() {
		$cache = array('ltitle' => array(), 'lpic' => array(), 'lneed' => array());
		$group = $this->_getUserGroupDs()->getAllGroups();
		foreach ($group as $key => $value) {
			$cache['ltitle'][$key] = $value['name'];
			$cache['lpic'][$key] = $value['image'];
			if ($value['type'] == 'member') $cache['lneed'][$key] = $value['points'];
		}
		return $cache;
	}

	/**
	 * 更新所有用户组的指定权限缓存group_right，主要用于帖子阅读页的显示权限判断 
	 *
	 * @param array $gkey
	 * @return void
	 */
	public function updateGroupRightCache($gkey = array()) {
		if ($gkey && !array_intersect($gkey, $this->_nkey)) return;
		Wekit::cache()->set('group_right', $this->getGroupRightCacheValue());
	}
	
	/**
	 * 获取所有用户组的指定权限缓存group_right
	 *
	 * @return array
	 */
	public function getGroupRightCacheValue() {
		$cache = array();
		$pm = $this->_getUserPermission()->fetchPermissionByRkey($this->_nkey);
		foreach ($pm as $key => $value) {
			$cache[$value['gid']][$value['rkey']] = $value['rvalue'];
		}
		return $cache;
	}
	
	/**
	 * 批量更新用户组的权限缓存
	 *
	 * @param array $gids 更新指定gid序列的权限缓存
	 * @return void
	 */
	public function updateGroupCache($gids = array()) {
		$group = $this->getGroupCacheValue($gids);
		foreach ($group as $key => $value) {
			Wekit::cache()->set('group', $value, array($key));
		}
	}
	
	/**
	 * 获取多个用户组的权限缓存
	 *
	 * @param array $gids 获取指定gid序列权限缓存内容
	 * @return array
	 */
	public function getGroupCacheValue($gids = array()) {
		if ($gids) {
			$group = $this->_getUserGroupDs()->fetchGroup($gids);
		} else {
			$group = $this->_getUserGroupDs()->getAllGroups();
		}
		$gids = array_keys($group);
		$result = $this->_getUserPermission()->fetchPermissionByGid($gids);
		$permission = array();
		foreach ($result as $key => $value) {
			$permission[$value['gid']][$value['rkey']] = array(
				'type' => $value['rtype'],
				'value' => $value['rvalue']
			);
		}
		foreach ($group as $key => $value) {
			$value['permission'] = $permission[$key] ? $permission[$key] : array();
			$group[$key] = $value;
		}
		return $group;
	}
	
	/**
	 * 获取单个用户组的权限缓存
	 *
	 * @param int $gid 获取指定gid的缓存内容
	 * @return array
	 */
	public function getGroupCacheValueByGid($gid) {
		$group = $this->getGroupCacheValue(array($gid));
		return $group ? current($group) : array();
	}

	/** 
	 * 获得用户Ds
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}

	/** 
	 * 获得用户组Ds
	 *
	 * @return PwUserGroups
	 */
	private function _getUserGroupDs() {
		return Wekit::load('usergroup.PwUserGroups');
	}

	private function _getUserPermission() {
		return Wekit::load('usergroup.PwUserPermission');
	}
}