<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 回收站帖子搜索条件
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwRecycleReplySo.php 11923 2012-06-14 09:17:27Z jieyin $
 * @package forum
 */

class PwRecycleReplySo {
	
	protected $_data = array();

	public function getData() {
		return $this->_data;
	}
	
	public function getOrderby() {
		return $this->_orderby;
	}

	/**
	 * 搜索帖子标题
	 */
	public function setKeywordOfTitle($keyword) {
		$this->_data['title_keyword'] = $keyword;
		return $this;
	}

	/**
	 * 搜索版块
	 *
	 * @param mixed $fid  int|array
	 */
	public function setFid($fid) {
		$this->_data['fid'] = $fid;
		return $this;
	}
	
	/**
	 * 搜索作者
	 */
	public function setAuthor($author) {
		$user = Wekit::load('user.PwUser')->getUserByName($author);
		$this->setAuthorId($user ? $user['uid'] : 0);
		return $this;
	}

	/**
	 * 搜索作者
	 *
	 * @param mixed $authorid  int|array
	 */
	public function setAuthorId($authorid) {
		$this->_data['created_userid'] = $authorid;
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

	public function setOperator($name) {
		$this->_data['operator'] = $name;
		return $this;
	}

	public function setOperatorTimeStart($time) {
		$this->_data['operate_time_start'] = $time;
		return $this;
	}

	public function setOperatorTimeEnd($time) {
		$this->_data['operate_time_end'] = $time + 86400;
		return $this;
	}

	public function orderbyCreatedTime($asc) {
		$this->_orderby['pid'] = (bool)$asc;
		return $this;
	}
}