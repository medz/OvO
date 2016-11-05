<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:education.srv.helper.PwEducationHelper');
Wind::import('SRV:education.dm.PwEducationDm');
/**
 * 教育经历
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: EducationController.php 28848 2013-05-28 02:21:12Z jieyin $
 * @package src.productions.u.controller.profile
 */
class EducationController extends BaseProfileController {
	protected $number = 10;

	/* (non-PHPdoc)
	 * @see BaseExtendsInjector::run()
	 */
	public function run() {
		$list = $this->_getService()->getEducationByUid($this->loginUser->uid, 100, true);
		$this->setOutput($list, 'list');
		$this->setOutput(PwEducationHelper::getDegrees(), 'degrees');
		$this->setOutput(PwEducationHelper::getEducationYear(), 'years');
		$this->setCurrentLeft();
		$this->setOutput(array('_tab' => 'education'), 'args');
	}
	
	/** 
	 * 添加教育经历
	 */
	public function addAction() {
		$educationDm = new PwEducationDm();
		$educationDm->setSchoolid($this->getInput('schoolid', 'post'));
		$educationDm->setStartTime($this->getInput('startYear', 'post'));
		$educationDm->setDegree($this->getInput('degree', 'post'));
		$educationDm->setUid($this->loginUser->uid);
		$educationDs = $this->_getDs();
		if (($result = $educationDs->addEducation($educationDm)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:education.add.success');
	}
	
	/**
	 * 删除教育经历
	 */
	public function deleteAction() {
		$id = $this->getInput('id', 'post');
		if (!$id) {
			$this->showError('operate.fail');
		}

		$educationDs = $this->_getDs();
		if (($result = $educationDs->deleteEducation($id, $this->loginUser->uid)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:education.delete.success');
	}
	
	/**
	 * 编辑教育经历
	 */
	public function editAction() {
		$educationDm = new PwEducationDm();
		$educationDm->setSchoolid($this->getInput('schoolid', 'post'));
		$educationDm->setStartTime($this->getInput('startYear', 'post'));
		$educationDm->setDegree($this->getInput('degree', 'post'));
		$educationDm->setUid($this->loginUser->uid);
		$educationDs = $this->_getDs();
		if (($result = $educationDs->editEducation($this->getInput('id'), $educationDm)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:education.update.success');
	}
	
	/** 
	 * 返回用户教育经历
	 *
	 * @return PwEducation
	 */
	private function _getDs() {
		return Wekit::load('SRV:education.PwEducation');
	}
	
	/** 
	 * 返回用户教育经历Service
	 *
	 * @return PwEducationService
	 */
	private function _getService() {
		return Wekit::load('SRV:education.srv.PwEducationService');
	}
}
