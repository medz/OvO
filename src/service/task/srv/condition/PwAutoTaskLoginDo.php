<?php
Wind::import('SRV:user.srv.login.PwUserLoginDoBase');
Wind::import('SRV:task.srv.PwTaskApply');
/**
 * 当前第一次登录DO
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.service.task.srv.condition
 */
class PwAutoTaskLoginDo extends PwUserLoginDoBase {
	
	/* (non-PHPdoc)
	 * @see PwUserLoginDoBase::welcome()
	 */
	public function welcome(PwUserBo $userBo, $ip) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		/* @var $behaviorDs PwUserBehavior */
		$behaviorDs = Wekit::load('user.PwUserBehavior');
		$info = $behaviorDs->getBehavior($userBo->uid, 'login_days');
		$time = $info['extend_info'] ? $info['extend_info'] : 0;
		if (!$time || (Pw::time2str($time, 'Y-m-d') < Pw::time2str(Pw::getTime(), 'Y-m-d'))) {
			/* @var $taskService PwTaskService */
			$taskService = Wekit::load('SRV:task.srv.PwTaskService');
			$userTask = new PwTaskApply($userBo->uid);
			$autoTaskIds = $taskService->getAutoApplicableTaskList($userBo->uid, 5);
			$userTask->autoApplies($autoTaskIds);
		}
		return true;
	}
}