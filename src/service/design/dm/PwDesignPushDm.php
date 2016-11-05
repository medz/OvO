<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignPushDm.php 19612 2012-10-16 08:43:42Z gao.wanggao $ 
 * @package 
 */
class PwDesignPushDm extends PwBaseDm {
	public $pushid;

	public function __construct($pushid = null) {
		if (isset($pushid))$this->pushid = (int)$pushid;
	}
	
	public function setFromid($fromid) {
		$this->_data['push_from_id'] = (int)$fromid;
		return $this;
	}
	
	public function setFormModel($model) {
		$this->_data['push_from_model'] = $model;
		return $this;
	}
	
	public function setModuleId($id) {
		$this->_data['module_id'] = (int)$id;
		return $this;
	}
	
	public function  setAuthorUid ($uid) {
		$this->_data['author_uid'] = (int)$uid;
		return $this;
	}
	
	public function setStandard($array) {
		$this->_data['push_standard'] =  serialize($array);
		return $this;
	}
	
	public function setStyle($bold, $underline, $italic, $color) {
		$this->_data['push_style'] = $bold.'|'.$underline.'|'.$italic.'|'.$color;
		return $this;
	}
	
	
	public function setOrderid($orderid) {
		$this->_data['push_orderid'] = (int)$orderid;
		return $this;
	}
	
	public function setExtend($extend) {
		$this->_data['push_extend'] = serialize($extend);
		return $this;
	}
	
	public function setCreatedUserid($uid) {
		$this->_data['created_userid'] = intval($uid);
		return $this;
	}
	
	public function setStatus($status) {
		$this->_data['status'] = intval($status);
		return $this;
	}
	
	public function setNeedNotice($isnotice) {
		$this->_data['neednotice'] = intval($isnotice);
		return $this;
	}
	
	public function setCheckUid($uid) {
		$this->_data['check_uid'] = intval($uid);
		return $this;
	}
	
	public function setCreatedTime($time) {
		$this->_data['created_time'] = intval($time);
		return $this;
	}
	
	public function setStartTime($time) {
		$this->_data['start_time'] = intval($time);
		return $this;
	}
	
	public function setEndTime($time) {
		$this->_data['end_time'] = intval($time);
		return $this;
	}
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if ($this->pushid < 1) return new PwError('fail');
		return true;
	}
	
	
	
}
?>