<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 任务奖励扩展-设置端
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: TaskRewardController.php 15745 2012-08-13 02:45:07Z xiaoxia.xuxx $
 * @package src.modules.task.admin
 */
class TaskRewardController extends AdminBaseController {

	/* (non-PHPdoc)
	 * @see AdminBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$var = unserialize($this->getInput('var'));
		if (is_array($var)) {
			$this->setOutput($var, 'reward');
		}
	}
	
	/* (non-PHPdoc)
	 * 任务奖励扩展-积分扩展
	 * @see WindController::run()
	 */
	public function run() {
		Wind::import('SRV:credit.bo.PwCreditBo');
		/* @var $pwCreditBo PwCreditBo */
		$pwCreditBo = PwCreditBo::getInstance();
		
		$this->setOutput($pwCreditBo, 'credit');
		$this->setTemplate('reward.reward_credit');
	}
	
	/**
	 * 任务奖励扩展-用户组扩展
	 */
	public function groupAction() {
		/* @var $userGroups PwUserGroups */
		$userGroups = Wekit::load('usergroup.PwUserGroups');
		$groupList = $userGroups->getGroupsByType('special');
		$this->setOutput($groupList, 'groups');
		$this->setTemplate('reward.reward_group');
	}
}