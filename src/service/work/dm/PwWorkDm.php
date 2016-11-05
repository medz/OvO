<?php

/**
 * 工作经历的DM
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwWorkDm.php 1532 2012-1-13上午11:01:28 xiaoxiao $
 * @package src.service.user.hooks.experience_work.dm
 */
class PwWorkDm  extends PwBaseDm {
	
	/** 
	 * 设置用户ID
	 *
	 * @param int $uid
	 * @return PwWorkDm
	 */
	public function setUid($uid) {
		$this->_data['uid'] = intval($uid);
		return $this;
	}
	
	/** 
	 * 设置工作单位名字
	 *
	 * @param string $company
	 * @return PwWorkDm
	 */
	public function setCompany($company) {
		$this->_data['company'] = trim($company);
		return $this;
	}
	
	/** 
	 * 设置开始时间
	 *
	 * @param int $year
	 * @param int $month
	 * @return PwWorkDm
	 */
	public function setStartTime($year, $month) {
		$this->_data['starty'] = intval($year);
		$this->_data['startm'] = intval($month);
		return $this;
	}
	
	/** 
	 * 设置结束时间
	 *
	 * @param int $year
	 * @param int $month
	 * @return PwWorkDm
	 */
	public function setEndTime($year, $month) {
		$this->_data['endy'] = intval($year);
		$this->_data['endm'] = intval($month);
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
		if (!isset($this->_data['uid'])) return new PwError('USER:work.illegal.request');
		if (!isset($this->_data['company']) || !$this->_data['company']) return new PwError('USER:work.update.company.require');
		if (!$this->_data['starty'] || !$this->_data['startm']) return new PwError('USER:work.update.start_time.require');
		return true;
	}
}