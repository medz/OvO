<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignComponentSo.php 10477 2012-05-24 09:15:44Z gao.wanggao $ 
 * @package 
 */
class PwDesignComponentSo {
	protected $_data = array();
	public function getData() {
		return $this->_data;
	}
	
	public function setModelFlag($flag) {
		$this->_data['model_flag'] = $flag;
	}
	

	public function setCompid($compid) {
		$this->_data['comp_id'] = $compid;
	}
	
	public function setCompname($name) {
		$this->_data['comp_name'] = $name;
	}
}
?>