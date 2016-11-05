<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * 前台管理日志LOGDM对象
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogDm.php 21359 2012-12-05 08:01:23Z xiaoxia.xuxx $
 * @package src.service.log.dm
 */
class PwLogDm extends PwBaseDm {

	/**
	 * 设置操作对象
	 * 
	 * @param int $uid
	 * @param string $username
	 * @return PwLogDm
	 */
	public function setOperatedUser($uid, $username = '') {
		$this->_data['operated_uid'] = intval($uid);
		$this->_data['operated_username'] = $username;
		return $this;
	}

	/**
	 * 设置操作者
	 *
	 * @param int $uid
	 * @param string $username
	 * @return PwLogDm
	 */
	public function setCreatedUser($uid, $username = '') {
		$this->_data['created_userid'] = intval($uid);
		$this->_data['created_username'] = $username;
		return $this;
	}

	/**
	 * 设置操作类型
	 *
	 * @param int $typeid
	 * @return PwLogDm
	 */
	public function setTypeid($typeid) {
		$this->_data['typeid'] = intval($typeid);
		return $this;
	}

	/**
	 * 设置版块ID
	 *
	 * @param int $fid
	 * @return PwLogDm
	 */
	public function setFid($fid) {
		$this->_data['fid'] = intval($fid);
		return $this;
	}

	/**
	 * 设置创建时间
	 *
	 * @param string $time
	 * @return PwLogDm
	 */
	public function setCreatedTime($time) {
		$this->_data['created_time'] = $time;
		return $this;
	}

	/**
	 * 设置帖子ID
	 *
	 * @param int $tid
	 * @return PwLogDm
	 */
	public function setTid($tid) {
		$this->_data['tid'] = intval($tid);
		return $this;
	}

	/**
	 * 设置帖子回复ID
	 *
	 * @param int $pid
	 * @return PwLogDm
	 */
	public function setPid($pid) {
		$this->_data['pid'] = intval($pid);
		return $this;
	}
	
	/**
	 * 设置IP地址
	 *
	 * @param string $ip
	 * @return PwLogDm
	 */
	public function setIp($ip) {
		$this->_data['ip'] = $ip;
		return $this;
	}

	/**
	 * 设置扩展信息
	 *
	 * @param string $extends
	 * @return PwLogDm
	 */
	public function setExtends($extends) {
		$this->_data['extends'] = $extends;
		return $this;
	}

	/**
	 * 设置操作描述
	 *
	 * @param string $content
	 * @return PwLogDm
	 */
	public function setContent($content) {
		$this->_data['content'] = $content;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!isset($this->_data['created_time'])) {
			$this->_data['created_time'] = Pw::getTime();
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
		return true;
	}
}

?>