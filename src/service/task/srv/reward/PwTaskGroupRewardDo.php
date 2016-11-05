<?php
Wind::import('SRV:task.srv.reward.PwTaskRewardDoBase');
Wind::import('SRV:user.dm.PwUserInfoDm');
/**
 * 用户组奖励
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskGroupRewardDo.php 22230 2012-12-19 21:45:20Z xiaoxia.xuxx $
 * @package src.service.srv.reward
 */
class PwTaskGroupRewardDo extends PwTaskRewardDoBase {
	
	/* (non-PHPdoc)
	 * @see PwTaskRewardDoInterface::gainReward()
	 */
	public function gainReward($uid, $reward, $taskname) {
		$userBo = Wekit::getLoginUser();
		list($id) = explode('-', $reward['value'], 2);
		$time = abs(intval($reward['time']));
		/* @var $userBelongDs PwUserBelong */
		$userBelongDs = Wekit::load('user.PwUserBelong');
		$info = $userBelongDs->getUserBelongs($uid);
		$_groups = array();
		foreach ($info as $_item) {
			$_groups[$_item['gid']] = $_item['endtime'];
		}
		$_groups[$id] = $time ? (Pw::getTime() + 24 * 3600 * $time) : 0;
		/* @var $userService PwUserService */
		$userService = Wekit::load('user.srv.PwUserService');
		list($gid, $groups) = $userService->caculateUserGroupid($userBo->gid, $_groups);
		$dm = new PwUserInfoDm($uid);
		$dm->setGroupid($gid)
			->setGroups($groups);
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$result = $userDs->editUser($dm, PwUser::FETCH_MAIN);
		if ($result instanceof PwError) return $result;
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskRewardDoBase::checkReward()
	 */
	public function checkReward($reward) {
		if (!$reward['time']) return new PwError('TASK:reward.group.num.require');
		if (!WindValidator::isNonNegative($reward['time'])) return new PwError('TASK:reward.group.num.isNonNegative');
		$reward['time'] = ceil($reward['time']);
		if ($reward['time'] <= 0) return new PwError('TASK:reward.group.num.isNonNegative');
		return parent::checkReward($reward);
	}
}