<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 帖子附件购买记录
 * 
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadAttachBuyDm.php 13665 2012-07-10 10:45:23Z jieyin $
 * @package attach
 */

class PwThreadAttachBuyDm extends PwBaseDm {
	
	public function setAid($aid) {
		$this->_data['aid'] = intval($aid);
		return $this;
	}

	public function setCreatedUserid($uid) {
		$this->_data['created_userid'] = intval($uid);
		return $this;
	}

	public function setCreatedTime($time) {
		$this->_data['created_time'] = $time;
		return $this;
	}
	
	public function setCtype($ctype) {
		$this->_data['ctype'] = intval($ctype);
		return $this;
	}

	public function setCost($cost) {
		$this->_data['cost'] = intval($cost);
		return $this;
	}
	
	public function _beforeAdd() {
		return true;
	}

	public function _beforeUpdate() {
		return true;
	}
}