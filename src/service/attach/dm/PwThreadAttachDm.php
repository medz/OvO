<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:attach.dm.PwAttachDm');

/**
 * 附件数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwThreadAttachDm.php 12856 2012-06-27 05:56:28Z jieyin $
 * @package attach
 */

class PwThreadAttachDm extends PwAttachDm {
	
	public function setFid($fid) {
		$this->_data['fid'] = intval($fid);
		return $this;
	}

	public function setTid($tid) {
		$this->_data['tid'] = intval($tid);
		return $this;
	}

	public function setPid($pid) {
		$this->_data['pid'] = intval($pid);
		return $this;
	}

	public function setWidth($width) {
		$this->_data['width'] = intval($width);
		return $this;
	}

	public function setHeight($height) {
		$this->_data['height'] = intval($height);
		return $this;
	}

	public function setSpecial($special) {
		$this->_data['special'] = intval($special);
		return $this;
	}

	public function setCost($cost) {
		$this->_data['cost'] = intval($cost);
		return $this;
	}

	public function setCtype($ctype) {
		$this->_data['ctype'] = intval($ctype);
		return $this;
	}

	public function addHits($hit) {
		$this->_increaseData['hits'] = intval($hit);
		return $this;
	}
}
?>