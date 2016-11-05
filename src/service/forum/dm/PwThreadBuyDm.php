<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 帖子购买记录
 * 
 * @author peihong <jhqblxt@gmail.com> Nov 23, 2011
 * @link
 * @version $Id: PwThreadBuyDm.php 10131 2012-05-18 03:54:34Z jieyin $
 * @license
 */

class PwThreadBuyDm extends PwBaseDm {
	
	public function setTid($tid) {
		$this->_data['tid'] = intval($tid);
		return $this;
	}

	public function setPid($pid) {
		$this->_data['pid'] = intval($pid);
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