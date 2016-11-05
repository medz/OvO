<?php
Wind::import('LIB:base.PwBaseDm');

/**
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwOnlineDm.php 20204 2012-10-24 09:14:08Z gao.wanggao $ 
 * @package 
 */
class PwOnlineDm extends PwBaseDm {
	
	public function setIp($ip) {
		$this->_data['ip'] = intval($ip);
		return $this;
	}
	
	public function setUid($uid) {
		$this->_data['uid'] = intval($uid);
		return $this;
	}
	
	public function setUsername ($username) {
		$this->_data['username'] = $username;
		return $this;
	}
	
	public function setModifytime ($time) {
		$this->_data['modify_time'] = intval($time);
		return $this;
	}
	
	public function setCreatedtime ($time) {
		$this->_data['created_time'] = intval($time);
		return $this;
	}
	
	public function setTid ($tid) {
		$this->_data['tid'] = intval($tid);
		return $this;
	}
	
	public function setFid ($fid) {
		$this->_data['fid'] = intval($fid);
		return $this;
	}
	
	public function setGid ($gid) {
		$this->_data['gid'] = intval($gid);
		return $this;
	}
	
	public function setRequest ($mca) {
		$this->_data['request'] = $mca;
		return $this;
	}
	
	protected function _beforeAdd() {
		if (!$this->_data['ip'] && !$this->_data['uid']) return new PwError('fail');
		return true;
	}
	
	protected function _beforeUpdate() {
		if (!$this->_data['ip'] && !$this->_data['uid']) return new PwError('fail');
		return true;
	}
	
}
?>