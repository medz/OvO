<?php

/**
 * 前台管理日志的搜索对象
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogSo.php 21170 2012-11-29 12:05:09Z xiaoxia.xuxx $
 * @package src.service.log.so
 */
class PwLogSo {
	private $_data = array();
	private $_orderData = array();
	/**
	 * 设置搜索条件-操作对象
	 *
	 * @param string $username
	 * @return PwLogSo
	 */
	public function setOperatedUsername($username) {
		if (!($username = trim($username))) return $this;
		$this->_data['operated_username'] = $username;
		$this->_orderData['operated_user'] = $username;
		return $this;
	}
	/**
	 * 设置搜索条件-操作者
	 *
	 * @param string $username
	 * @return PwLogSo
	 */
	public function setCreatedUsername($username) {
		if (!($username = trim($username))) return $this;
		$this->_data['created_username'] = $username;
		$this->_orderData['created_user'] = $username;
		return $this;
	}
	
	/**
	 * 设置搜索条件-操作类型
	 *
	 * @param int $typeid
	 * @return PwLogSo
	 */
	public function setTypeid($typeid) {
		if (0 > ($typeid = intval($typeid))) return $this;
		$this->_data['typeid'] = $typeid;
		$this->_orderData['typeid'] = $typeid;
		return $this;
	}
	
	/**
	 * 设置搜索条件-版块ID
	 *
	 * @param int $fid
	 * @return PwLogSo
	 */
	public function setFid($fid) {
		if (0 >= ($fid = intval($fid))) return $this;
		$this->_data['fid'] = $fid;
		$this->_orderData['fid'] = $fid;
		return $this;
	}
	
	/**
	 * 设置搜索条件-开始搜索的时间
	 *
	 * @param string $time
	 * @return PwLogSo
	 */
	public function setStartTime($time) {
		if (!($time = trim($time))) return $this;
		$this->_data['start_time'] = Pw::str2time($time);
		$this->_orderData['start_time'] = $time;
		return $this;
	}
	
	/**
	 * 设置搜索条件-搜索时间结束
	 *
	 * @param string $time
	 * @return PwLogSo
	 */
	public function setEndTime($time) {
		if (!($time = trim($time))) return $this;
		$this->_data['end_time'] = Pw::str2time($time);
		$this->_orderData['end_time'] = $time;
		return $this;
	}
	
	/**
	 * 设置搜索条件-IP地址
	 *
	 * @param string $ip
	 * @return PwLogSo
	 */
	public function setIp($ip) {
		if (!($time = trim($ip))) return $this;
		$this->_data['ip'] = $ip;
		$this->_orderData['ip'] = $ip;
		return $this;
	}
	/**
	 * 设置搜索条件-关键字
	 *
	 * @param string $key
	 * @return PwLogSo
	 */
	public function setKeywords($key) {
		if (!($key = trim($key))) return $this;
		$this->_data['keywords'] = $key;
		$this->_orderData['keywords'] = $key;
		return $this;
	}
	
	/**
	 * 获取搜索的条件--DAO
	 *
	 * @return  array
	 */
	public function getCondition() {
		return $this->_data;
	}
	
	/**
	 * 获取搜索的条件
	 *
	 * @return array
	 */
	public function getSearchData() {
		return $this->_orderData;
	}
}