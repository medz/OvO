<?php

/**
 * 用户禁止DM
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserBanInfoDm.php 23904 2013-01-17 05:27:48Z xiaoxia.xuxx $
 * @package src.service.user.dm
 */
class PwUserBanInfoDm extends PwBaseDm {
	
	/**
	 * 设置用户ID
	 *
	 * @param int $uid
	 * @return PwUserbanInfoDm
	 */
	public function setUid($uid) {
		$this->_data['uid'] = intval($uid);
		return $this;
	}
	
	/**
	 * 设置类型
	 *
	 * @param int $typeid
	 * @return PwUserbanInfoDm
	 */
	public function setTypeid($typeid) {
		$this->_data['typeid'] = $typeid;
		return $this;
	}
	
	/**
	 * 设置类型ID
	 *
	 * @param int $fid
	 * @return PwUserbanInfoDm
	 */
	public function setFid($fid) {
		$this->_data['fid'] = intval($fid);
		return $this;
	}
	
	/**
	 * 设置创建用户
	 *
	 * @param int $userid
	 * @return PwUserbanInfoDm
	 */
	public function setCreatedUid($userid) {
		$this->_data['created_userid'] = $userid;
		return $this;
	}
	
	/**
	 * 设置开始时间
	 *
	 * @param int $time
	 * @return PwUserbanInfoDm
	 */
	public function setCreateTime($time) {
		$this->_data['created_time'] = $time;
		return $this;
	}
	
	/**
	 * 设置禁止时间
	 *
	 * @param int $time
	 * @return PwUserbanInfoDm
	 */
	public function setEndTime($time) {
		$this->_data['end_time'] = $time;
		return $this;
	}
	
	/**
	 * 设置禁止原因
	 *
	 * @param string $reason
	 * @return PwUserbanInfoDm
	 */
	public function setReason($reason) {
		$this->_data['reason'] = trim($reason);
		return $this;
	}
	
	/**
	 * 设置操作者名字
	 * 
	 * @param string $username
	 * @return PwUserbanInfoDm
	 */
	public function setOperator($username) {
		$this->username = $username;
		return $this;
	}
	
	/**
	 * 获得操作者名字
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->username;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!$this->getField('created_time')) $this->_data['created_time'] = Pw::getTime();
		if (!$this->getField('uid')) return new PwError('USER:ban.type.require');
		if (!$this->getField('typeid')) return new PwError('USER:ban.type.require');
		if (!$this->getField('reason')) return new PwError('USER:ban.reason.require');
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (!$this->getField('typeid')) return new PwError('USER:ban.type.require');
		if (!$this->getField('reason')) return new PwError('USER:ban.reason.require');
		return true;
	}
}