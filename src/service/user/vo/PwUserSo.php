<?php

/**
 * 用户搜索
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserSo.php 20572 2012-10-31 06:50:17Z jinlong.panjl $
 * @package service.user.vo
 */
class PwUserSo {
	private $_data = array();
	protected $_orderby = array();
	
	/**
	 * 根据用户名搜索
	 *
	 * @param string $name
	 * @return PwUserSo
	 */
	public function setUsername($name) {
		$this->_data['username'] = $name;
		return $this;
	}
	
	/**
	 * 设置查询的用户ID
	 *
	 * @param int|array $uid
	 * @return PwUserSo
	 */
	public function setUid($uid) {
		$this->_data['uid'] = $uid;
		return $this;
	}
	
	/**
	 * 设置查询的email
	 *
	 * @param string $email
	 * @return PwUserSo
	 */
	public function setEmail($email) {
		$this->_data['email'] = $email;
		return $this;
	}
	
	/**
	 * 设置查询的用户组
	 *
	 * @param int|array $gid
	 * @return PwUserSo
	 */
	public function setGid($gid) {
		$this->_data['gid'] = $gid;
		return $this;
	}
	
	/**
	 * 设置查询的用户组
	 *
	 * @param int|array $memberid
	 * @return PwUserSo
	 */
	public function setMemberid($memberid) {
		$this->_data['memberid'] = $memberid;
		return $this;
	}
	
	/**
	 * 设置用户的性别  | 该查询字段没有索引  
	 *
	 * @param int $gender
	 * @return PwUserSo
	 */
	public function setGender($gender) {
		$this->_data['gender'] = $gender == 1 ? 1 : 0;
		return $this;
	}
	
	/**
	 * 设置居住地地址
	 *
	 * @param int $areaid
	 * @return PwUserSo
	 */
	public function setLocation($areaid) {
		$this->_data['location'] = $areaid;
		return $this;
	}
	
	/**
	 * 设置家庭地址
	 *
	 * @param int $areaid
	 * @return PwUserSo
	 */
	public function setHometown($areaid) {
		$this->_data['hometown'] = $areaid;
		return $this;
	}
	
	/**
	 * 设置查询的regdate注册时间
	 *
	 * @param int $regdate
	 * @return PwUserSo
	 */
	public function setRegdate($regdate) {
		$this->_data['regdate'] = $regdate;
		return $this;
	}
	
	/**
	 * 设置查询的postnum发帖量 | 无索引  请谨慎使用
	 *
	 * @param int $asc
	 * @return PwUserSo
	 */
	public function orderbyPostnum($asc) {
		$this->_orderby['postnum'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 设置查询的lastvisit上次访问时间 | 无索引  请谨慎使用
	 *
	 * @param int $asc
	 * @return PwUserSo
	 */
	public function orderbyLastvisit($asc) {
		$this->_orderby['lastvisit'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 设置查询的lastpost上次递交帖子的时间 | 无索引  请谨慎使用
	 *
	 * @param int $asc
	 * @return PwUserSo
	 */
	public function orderbyLastpost($asc) {
		$this->_orderby['lastpost'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 设置查询的regdate注册时间 | 无索引  请谨慎使用 | 由于注册时间后台可以更改，所以不能使用UID
	 *
	 * @param int $asc
	 * @return PwUserSo
	 */
	public function orderbyRegdate($asc) {
		$this->_orderby['regdate'] = (bool)$asc;
		return $this;
	}
	
	/**
	 * 获得查询数据
	 *
	 * @return array
	 */
	public function getData() {
		return $this->_data;
	}
	
	/**
	 * 获得排序数据
	 *
	 * @return array
	 */
	public function getOrderby() {
		return $this->_orderby;
	}
}