<?php

/**
 * 教育经历的DM
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwEducationDm.php 1532 2012-1-13上午11:01:28 xiaoxiao $
 * @package src.service.user.hooks.experience_education.dm
 */
class PwEducationDm extends PwBaseDm {
	
	/** 
	 * 设置用户ID
	 *
	 * @param int $uid
	 * @return PwEducationDm
	 */
	public function setUid($uid) {
		$this->_data['uid'] = intval($uid);
		return $this;
	}
	
	/** 
	 * 设置教育单位名字
	 *
	 * @param string $school
	 * @return PwEducationDm
	 */
	public function setSchoolid($school) {
		$this->_data['schoolid'] = intval($school);
		return $this;
	}
	
	/**
	 * 设置学历
	 *
	 * @param string $degree
	 * @return PwEducationDm
	 */
	public function setDegree($degree) {
		$this->_data['degree'] = intval($degree);
		return $this;
	}
	
	/** 
	 * 设置开始时间
	 *
	 * @param int $year
	 * @return PwEducationDm
	 */
	public function setStartTime($year) {
		$this->_data['start_time'] = intval($year);
		return $this; 
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		return $this->check();
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		return $this->check();
	}
	
	/**
	 * 检查数据
	 *
	 * @return PwError
	 */
	protected function check() {
		if (!isset($this->_data['uid'])) return new PwError('USER:education.illegal.request');
		if (!isset($this->_data['schoolid']) || !$this->_data['schoolid']) return new PwError('USER:education.update.school.require');
		if (!isset($this->_data['start_time']) || !$this->_data['start_time']) return new PwError('USER:education.update.start_time.require');
		$this->_data['start_time'] = PwEducationHelper::checkEducationYear($this->_data['start_time']);
		if (!PwEducationHelper::checkDegree($this->_data['degree'])) return new PwError('USER:education.update.degree.error');
		return true;
	}
}