<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMedalLogDm.php 5752 2012-03-10 06:01:04Z gao.wanggao $ 
 * @package 
 */
class PwMedalLogDm extends PwBaseDm {
	
	public $logid;
	
	public function __construct($logid = null){
		if (isset($logid))$this->logid = (int)$logid;
	}
	
	public function setMedalid($id) {
		$this->_data['medal_id'] = (int)$id;
		return $this;
	}
	
	public function setUid($uid) {
		$this->_data['uid'] = (int)$uid;
		return $this;
	}
	
	public function setAwardStatus($status) {
		$this->_data['award_status'] = (int)$status;
		return $this;
	}
	
	public function setCreatedTime($time) {
		$this->_data['created_time'] = (int)$time;
		return $this;
	}
	
	public function setExpiredTime($time) {
		$this->_data['expired_time'] = (int)$time;
		return $this;
	}
	
	public function setLogOrder($orderid) {
		$this->_data['log_order'] = (int)$orderid;
		return $this;
	}
	
	protected function _beforeAdd() {
		if (empty($this->_data['medal_id']) || empty($this->_data['uid'])) {
			return new PwError('MEDAL:fail');
		}
		return true;
	}
	
	protected function _beforeUpdate() {
		if (empty($this->logid)) {
			return new PwError('MEDAL:fail');
		}
		return true;
	}
	
}
?>