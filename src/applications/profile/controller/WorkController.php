<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:work.dm.PwWorkDm');

/**
 * 用户资料-工作经历扩展
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: WorkController.php 28852 2013-05-28 02:46:06Z jieyin $
 * @package src.productions.u.controller.profile
 */
class WorkController extends BaseProfileController {
	protected $number = 10;

	/* (non-PHPdoc)
	 * @see BaseExtendsInjector::run()
	 */
	public function run() {
		$page = abs(intval($this->getInput('page')));
		($page < 1) && $page = 1;
		$count = $this->_getDs()->countByUid($this->loginUser->uid);
		$list = array();
		if ($count > 0) {
			$totalPage = ceil($count / $this->number);
			$page > $totalPage && $page = $totalPage;
			$start = ($page - 1) * $this->number;
			$list = $this->_getDs()->getByUid($this->loginUser->uid, $this->number, $start);
		}
		$this->setCurrentLeft('profile', 'work');
		$this->setOutput(array('_tab' => 'work'), 'args');
		$this->setOutput($count, 'count');
		$this->setOutput($list, 'list');
		$this->setOutput($page, 'page');
		$this->setOutput(ceil($count / $this->number), 'page_total');
		$this->setYearAndMonth();
	}
	
	/** 
	 * 添加工作经历
	 */
	public function addAction() {
		$workDm = new PwWorkDm();
		$workDm->setCompany($this->getInput('company'), 'post');
		$workDm->setStartTime($this->getInput('startYear', 'post'), $this->getInput('startMonth', 'post'));
		$workDm->setEndTime($this->getInput('endYear', 'post'), $this->getInput('endMonth', 'post'));
		$workDm->setUid($this->loginUser->uid);
		
		$workDs = $this->_getDs();
		if (($result = $workDs->addWorkExperience($workDm)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:work.add.success');
	}
	
	/**
	 * 删除工作经历
	 */
	public function deleteAction() {
		$id = $this->getInput('id', 'post');
		if (!$id) {
			$this->showError('operate.fail');
		}

		$workDs = $this->_getDs();
		if (($result = $workDs->deleteWorkExperience($id, $this->loginUser->uid)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:work.delete.success');
	}
	
	/**
	 * 编辑工作经历
	 */
	public function editAction() {
		$workDm = new PwWorkDm();
		$workDm->setCompany($this->getInput('company', 'post'));
		$workDm->setStartTime($this->getInput('startYear', 'post'), $this->getInput('startMonth', 'post'));
		$workDm->setEndTime($this->getInput('endYear', 'post'), $this->getInput('endMonth', 'post'));
		$workDm->setUid($this->loginUser->uid);
		$workDs = $this->_getDs();
		if (($result = $workDs->editWorkExperience($this->getInput('id', 'post'), $workDm)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:work.update.success');
	}
	
	/**
	 * 获得年及月列表
	 */
	private function setYearAndMonth() {
		$tyear = Pw::time2str(Pw::getTime(), 'Y');
		$this->setOutput(range($tyear, $tyear-100, -1), 'years');
		$this->setOutput(range(1, 12, 1), 'months');
	}
	
	/** 
	 * 返回用户工作经历
	 *
	 * @return PwWork
	 */
	private function _getDs() {
		return Wekit::load('SRV:work.PwWork');
	}
}