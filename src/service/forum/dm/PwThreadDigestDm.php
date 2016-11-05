<?php

/**
 * 加精DM
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwThreadDigestDm.php 22320 2012-12-21 08:14:25Z xiaoxia.xuxx $
 * @package src.service.forum.dm
 */
class PwThreadDigestDm extends PwBaseDm {
	public $tid;
	
	/**
	 * 构造函数
	 *
	 * @param int $tid
	 */
	public function __construct($tid) {
		 $this->tid = $tid;
	}
	
	/**
	 * 设置tid
	 *
	 * @param int $tid
	 * @return PwThreadDigestDm
	 */
	public function setTid($tid) {
		$this->tid = $tid;
		return $this;
	}
	
	/**
	 * 设置板块ID
	 *
	 * @param int $fid
	 * @return PwThreadDigestDm
	 */
	public function setFid($fid) {
		$this->_data['fid'] = $fid;
		return $this;
	}
	
	/**
	 * 设置版块的分类ID
	 *
	 * @param int $cid
	 * @return PwThreadDigestDm
	 */
	public function setCid($cid) {
		$this->_data['cid'] = $cid;
		return $this;
	}
	
	/**
	 * 设置帖子的状态
	 *
	 * @param int $disabled
	 * @return PwThreadDigestDm
	 */
	public function setDisabled($disabled) {
		$this->_data['disabled'] = $disabled;
		return $this;
	}
	
	/**
	 * 设置主题类型的ID
	 *
	 * @param int $topic_type
	 * @return PwThreadDigestDm
	 */
	public function setTopicType($topic_type) {
		$this->_data['topic_type'] = $topic_type;
		return $this;
	}
	
	/**
	 * 设置帖子的创建时间
	 *
	 * @param int $created_time
	 * @return PwThreadDigestDm
	 */
	public function setCreatedTime($created_time) {
		$this->_data['created_time'] = $created_time;
		return $this;
	}
	
	/**
	 * 设置帖子的最后回复时间
	 *
	 * @param int $lastpost_time
	 * @return PwThreadDigestDm
	 */
	public function setLastpostTime($lastpost_time) {
		$this->_data['lastpost_time'] = $lastpost_time;
		return $this;
	}
	
	/**
	 * 加精的操作者
	 *
	 * @param string $username
	 * @param int $uid
	 * @param int $time
	 * @return PwThreadDigestDm
	 */
	public function setOperator($username, $uid, $time) {
		$this->_data['operator'] = $username;
		$this->_data['operator_userid'] = $uid;
		$this->_data['operator_time'] = $time;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!$this->tid) return new PwError('BBS:digest.tid.require');
		$this->_data['digest'] = 1;
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (!$this->tid) return new PwError('BBS:digest.tid.require');
		return true;
	}
}