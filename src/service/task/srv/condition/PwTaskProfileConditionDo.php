<?php
Wind::import('SRV:task.srv.PwTaskComplete');
Wind::import('SRV:task.srv.base.PwTaskCompleteInterface');
/**
 * 完善资料-任务
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.service.task.srv.condition
 */
class PwTaskProfileConditionDo implements PwTaskCompleteInterface {
	
	/* @var $userDm PwUserInfoDm */
	private $userDm = null;
	
	/**
	 * 更新用户
	 *
	 * @param PwUserInfoDm $userDm
	 * @return boolean
	 */
	public function editUser($userDm) {
		if (!Wekit::C('site', 'task.isOpen')) return true;
		$this->userDm = $userDm;
		$taskCompleteBp = new PwTaskComplete($userDm->uid, $this);
		return $taskCompleteBp->doTask('member', 'profile');
	}

	/* (non-PHPdoc)
	 * @see PwTaskCompleteInterface::doTask()
	 */
	public function doTask($conditions, $step) {
		$isComplete = false;
		if (isset($step['percent']) && $step['percent'] == '100%') {
			$isComplete = true;
		} else {
			$step = $this->_caculatePercent($step);
			$step['percent'] == '100%' && $isComplete = true;
		}
		return array('isComplete' => $isComplete, 'step' => $step);
	}
	
	/**
	 * 计算完成进度
	 *
	 * @param array $step
	 * @return array
	 */
	private function _caculatePercent($step) {
		$_temp = array('bday', 'bmonth', 'bday', 'gender', 'hometown', 'location', 'homepage', 'profile', 'bbs_sign');
		$percent = 0;
		foreach ($_temp as $key) {
			if ($percent == 5) break;
			if ($this->userDm->getField($key)) $percent ++;
		}
		$percent && $step['step'] = $percent;
		$step['percent'] = intval((($step['step'] / 5) * 100)) . '%';
		return $step;
	}
}