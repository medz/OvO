<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignAsynImageDm.php 22292 2012-12-21 05:09:20Z gao.wanggao $ 
 * @package 
 */
class PwDesignAsynImageDm extends PwBaseDm {
	public $id;

	public function __construct($id = null) {
		if (isset($id))$this->id = (int)$id;
	}
	
	public function setPath($path) {
		$this->_data['path'] = $path;
		return $this;
	}
	
	public function setThumb($thumb) {
		$this->_data['thumb'] = $thumb;
		return $this;
	}
	
	public function setWidth($width) {
		$this->_data['width'] = (int)$width;
		return $this;
	}
	
	public function setHeight($height) {
		$this->_data['height'] = (int)$height;
		return $this;
	}
	
	public function setModuleid($moduleid) {
		$this->_data['moduleid'] = (int)$moduleid;
		return $this;
	}
	
	public function setDataid($dataid) {
		$this->_data['data_id'] = (int)$dataid;
		return $this;
	}
	
	public function setSign($sign) {
		$this->_data['sign'] = $sign;
		return $this;
	}
	
	public function setStatus($status) {
		$this->_data['status'] = (int)$status;
		return $this;
	}
	
	
	protected function _beforeAdd() {
		return true;
	}
	
	protected function _beforeUpdate() {
		if ($this->id < 1) return new PwError('fail');
		return true;
	}
}
?>