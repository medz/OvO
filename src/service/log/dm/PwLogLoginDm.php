<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * 前台管理日志LOGDM对象
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogLoginDm.php 21359 2012-12-05 08:01:23Z xiaoxia.xuxx $
 * @package src.service.log.dm
 */
class PwLogLoginDm extends PwBaseDm {
	/**
	 * 构造函数
	 *
	 * @param int $uid
	 */
	public function __construct($uid) {
		$this->_data['uid'] = intval($uid);
	}
	
	/**
	 * 设置登录的用户名
	 *
	 * @param string $username
	 * @return PwLogLoginDm
	 */
	public function setUsername($username) {
		$this->_data['username'] = $username;
		return $this;
	}

	/**
	 * 设置尝试错误的类型
	 * 
	 * @param int $typeid
	 * @return PwLogLoginDm
	 */
	public function setTypeid($typeid = '') {
		$this->_data['typeid'] = intval($typeid);
		return $this;
	}

	/**
	 * 设置创建时间
	 *
	 * @param string $time
	 * @return PwLogLoginDm
	 */
	public function setCreatedTime($time) {
		$this->_data['created_time'] = $time;
		return $this;
	}
	
	/**
	 * 设置IP地址
	 *
	 * @param string $ip
	 * @return PwLogLoginDm
	 */
	public function setIp($ip) {
		$this->_data['ip'] = $ip;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!isset($this->_data['created_time'])) {
			$this->_data['created_time'] = Pw::getTime();
		}
		if (!isset($this->_data['typeid']) || !in_array($this->_data['typeid'], array(PwLogLogin::ERROR_PWD, PwLogLogin::ERROR_SAFEQ))) {
			$this->_data['typeid'] = PwLogLogin::ERROR_PWD;
		}
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (!isset($this->_data['created_time'])) {
			$this->_data['created_time'] = Pw::getTime();
		}
		if (!isset($this->_data['typeid']) || !in_array($this->_data['typeid'], array(PwLogLogin::ERROR_PWD, PwLogLogin::ERROR_SAFEQ))) {
			$this->_data['typeid'] = PwLogLogin::ERROR_PWD;
		}
		return true;
	}
}

?>