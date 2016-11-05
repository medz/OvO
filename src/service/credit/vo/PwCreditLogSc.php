<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子搜索条件
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditLogSc.php 8147 2012-04-16 09:37:12Z jieyin $
 * @package forum
 */

class PwCreditLogSc {
	
	protected $_data = array();

	public function getData() {
		return $this->_data;
	}

	public function hasData() {
		return !empty($this->_data);
	}
	
	/**
	 * 搜索帖子标题
	 */
	public function setCtype($ctype) {
		$this->_data['ctype'] = $ctype;
		return $this;
	}
	
	/**
	 * 搜索作者
	 */
	public function setUserid($uid) {
		$this->_data['created_userid'] = $uid;
		return $this;
	}
	
	/**
	 * 发帖时间区间，起始
	 */
	public function setCreateTimeStart($time) {
		$this->_data['created_time_start'] = $time;
		return $this;
	}
	
	/**
	 * 发帖时间区间，结束
	 */
	public function setCreateTimeEnd($time) {
		$this->_data['created_time_end'] = $time + 86400;
		return $this;
	}
	
	public function setAward($award) {
		$this->_data['award'] = intval($award);
	}
}