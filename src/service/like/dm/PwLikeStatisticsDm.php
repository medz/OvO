<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$ 
 * @package 
 */
class PwLikeStatisticsDm extends PwBaseDm {

	public function setSignkey($key) {
		$this->_data['signkey'] = $key;
		return $this;
	}
	
	public function setNumber($number) {
		$this->_data['number'] = (int)$number;
		return $this;
	}
	
	public function setLikeid($likeid) {
		$this->_data['likeid'] = (int)$likeid;
		return $this;
	}
	
	public function setTypeid($typeid) {
		$this->_data['typeid'] = (int)$typeid;
		return $this;
	}
	
	public function setFromid($fromid) {
		$this->_data['fromid'] = (int)$fromid;
		return $this;
	}
	
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if ($this->likeid < 1) return new PwError('BBS:like.likeid.empty');
		return true;
	}
}
?>