<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户服务接口(不常用的业务逻辑)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserMiscService.php 20650 2012-11-01 09:10:44Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwUserMiscService {
	
	/**
	 * 根据版主名单更新数据<1.pw_user_belong 2.pw_user中的groups字段>
	 *
	 * @param array $manager 所有的版主名单
	 */
	public function updateManager($manager) {
		$newManager = Wekit::load('user.PwUser')->fetchUserByName($manager);
		$uids = array_keys(Wekit::load('user.PwUserBelong')->getUserByGid(5));
		$oldManager = Wekit::load('user.PwUser')->fetchUserByUid($uids);
		if (!$newManager && !$oldManager) {
			return;
		}
		$newUids = array_keys($newManager);
		$oldUids = array_keys($oldManager);
		$add = array_diff($newUids, $oldUids);
		$del = array_diff($oldUids, $newUids);
		if (!$add && !$del) {
			return;
		}
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$belongs = $this->getBelongs(array_merge($add, $del));
		foreach ($add as $uid) {
			$dm = new PwUserInfoDm($uid);
			$belong = isset($belongs[$uid]) ? $belongs[$uid] : array();
			if ($newManager[$uid]['groupid']) {
				$belong[5] = 0;
				$dm->setGroupid($newManager[$uid]['groupid']);
			} else {
				$dm->setGroupid(5);
			}
			$dm->setGroups($belong);
			Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_MAIN);
		}
		foreach ($del as $uid) {
			$dm = new PwUserInfoDm($uid);
			$belong = isset($belongs[$uid]) ? $belongs[$uid] : array();
			unset($belong[5]);
			if ($oldManager[$uid]['groupid'] == 5) {
				$dm->setGroupid(0);
			} else {
				$dm->setGroupid($oldManager[$uid]['groupid']);
			}
			$dm->setGroups($belong);
			Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_MAIN);
		}
	}

	/**
	 * 获取用户ID列表里的用户附加组信息
	 *
	 * @param array $uids
	 * @return array
	 */
	public function getBelongs($uids) {
		$result = array();
		$array = Wekit::load('user.PwUserBelong')->fetchUserByUid($uids);
		foreach ($result as $key => $value) {
			$result[$value['uid']][$value['gid']] = $value['endtime'];
		}
		return $result;
	}
	
	/**
	 * 判断被选为版主的用户是否都是合法用户
	 * 
	 * 这些用户不允许是有禁言用户和未验证用户
	 *
	 * @param array $mangers
	 * @return PwError|true
	 */
	public function filterForumManger($mangers) {
		$backGids = array(1 => '默认组', 2 => '游客', 6 => '禁言用户', 7 => '未验证会员');
		$managerList = Wekit::load('user.PwUser')->fetchUserByName($mangers);
		$_tmp = array();
		foreach ($managerList as $uid => $_item) {
			if (array_key_exists($_item['groupid'], $backGids)) {
				$_tmp[$_item['groupid']][] = $_item['username'];
			}
		}
		if (!$_tmp) return true;
		$back = array();
		foreach ($_tmp as $key => $_value) {
			$back[] = $backGids[$key] . ":" . implode(', ', $_value);
		}
		return new PwError('BBS:forum.back.manager', array('{back}' => implode(';', $back)));
	}
}