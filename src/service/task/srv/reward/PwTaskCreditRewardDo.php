<?php
Wind::import('SRV:task.srv.reward.PwTaskRewardDoBase');
Wind::import('SRV:credit.bo.PwCreditBo');
/**
 * 奖励-积分
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwTaskCreditRewardDo.php 18510 2012-09-19 01:55:21Z jieyin $
 * @package wind
 */
class PwTaskCreditRewardDo extends PwTaskRewardDoBase {

	/* (non-PHPdoc)
	 * @see PwTaskRewardDoInterface::gainReward()
	 */
	public function gainReward($uid, $reward, $taskname) {
		$num = $reward['num'];
		list($id) = explode('-', $reward['value'], 2);
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->addLog('task_reward', array($id => $reward['num']), Wekit::getLoginUser(), array('taskname' => $taskname));
		return $creditBo->sets($uid, array($id => $reward['num']));
	}
	
	/* (non-PHPdoc)
	 * @see PwTaskRewardDoBase::checkReward()
	 */
	public function checkReward($reward) {
		if (!$reward['num']) return new PwError('TASK:reward.credit.num.require');
		if (!WindValidator::isNonNegative($reward['num'])) return new PwError('TASK:reward.credit.num.isNonNegative');
		$reward['num'] = ceil($reward['num']);
		return parent::checkReward($reward);
	}
	
	/**
	 * 获得用户DS
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
}