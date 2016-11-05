<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 新鲜事基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFresh.php 19501 2012-10-15 08:36:20Z jieyin $
 * @package fresh
 */

class PwFresh {
	
	const TYPE_THREAD_TOPIC = 1; //新鲜事类型-帖子
	const TYPE_THREAD_REPLY = 2; //新鲜事类型-回复
	const TYPE_WEIBO = 3;
	
	/**
	 * 获取新鲜事
	 *
	 * @param int $id 新鲜事id
	 * @return array
	 */
	public function getFresh($id) {
		if (empty($id)) return array();
		return $this->_getDao()->getFresh($id);
	}
	
	/**
	 * 获取多条新鲜事
	 *
	 * @param array $ids 新鲜事id序列
	 * @return array
	 */
	public function fetchFresh($ids) {
		if (empty($ids) || !is_array($ids)) return array();
		return $this->_getDao()->fetchFresh($ids);

	}
	
	/**
	 * 统计用户的新鲜事条目
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countFreshByUid($uid) {
		if (empty($uid)) return 0;
		return $this->_getDao()->countFreshByUid($uid);
	}

	/**
	 * 获取用户的新鲜事
	 *
	 * @param int $uid 用户id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getFreshByUid($uid, $limit = 20, $offset = 0) {
		if (empty($uid)) return array();
		return $this->_getDao()->getFreshByUid($uid, $limit, $offset);
	}
	
	/**
	 * 获取我关注的新鲜事
	 *
	 * @param int $uid 用户id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAttentionFresh($uid, $limit = 20, $offset = 0) {
		if (!$fresh = $this->_getRelationDao()->get($uid, $limit, $offset)) {
			return array();
		}
		$fresh_ids = $result = array();
		foreach ($fresh as $value) {
			$fresh_ids[] = $value['fresh_id'];
		}
		$array = $this->_getDao()->fetchFresh($fresh_ids);

		foreach ($fresh_ids as $key => $value) {
			if (isset($array[$value])) $result[$value] = $array[$value];
		}
		return $result;
	}
	
	/**
	 * 统计我关注的新鲜事条目总数
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countAttentionFresh($uid) {
		return $this->_getRelationDao()->count($uid);
	}
	
	/**
	 * 统计用户(A)关注的指定用户列表的新鲜事条目
	 *
	 * @param int $uid 用户(A)
	 * @param array $uids 指定用户列表
	 * @return int
	 */
	public function countAttentionFreshByUid($uid, $uids) {
		if (empty($uid) || empty($uids) || !is_array($uids)) return 0;
		return $this->_getRelationDao()->countByUid($uid, $uids);
	}

	/**
	 * 获取用户(A)关注的指定用户列表的新鲜事
	 *
	 * @param int $uid 用户(A)
	 * @param array $uids 指定用户列表
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function fetchAttentionFreshByUid($uid, $uids, $limit = 20, $offset = 0) {
		if (empty($uid) || empty($uids) || !is_array($uids)) return array();
		if (!$fresh = $this->_getRelationDao()->fetchAttentionFreshByUid($uid, $uids, $limit, $offset)) {
			return array();
		}
		$fresh_ids = $result = array();
		foreach ($fresh as $value) {
			$fresh_ids[] = $value['fresh_id'];
		}
		$array = $this->_getDao()->fetchFresh($fresh_ids);

		foreach ($fresh_ids as $key => $value) {
			if (isset($array[$value])) $result[$value] = $array[$value];
		}
		return $result;
	}
	
	/**
	 * 获取某个类型的新鲜事
	 *
	 * @param int $type 新鲜事来源类型，必为常量 SEND_* 中的一种
	 * @param int $srcIds ID序列
	 * @return array
	 */
	public function getFreshByType($type, $srcIds) {
		if (empty($srcIds) || !is_array($srcIds)) return array();
		return $this->_getDao()->getFreshByType($type, $srcIds);
	}
	
	/**
	 * 发送新鲜事
	 *
	 * @param int $uid 发送者id
	 * @param int $type 新鲜事来源类型，必为常量 SEND_* 中的一种
	 * @param int $srcId 新鲜事来源id
	 * @return int
	 */
	public function send($uid, $type, $srcId) {
		if (!$uid || !$srcId) {
			return 0;
		}
		$data = array(
			'type' => $type,
			'src_id' => $srcId,
			'created_userid' => $uid,
			'created_time' => Pw::getTime()
		);
		if (!$freshId = $this->_getDao()->addFresh($data)) {
			return 0;
		}
		$this->_addRelation($uid, $freshId, $type);
		return $freshId;
	}

	/**
	 * 批量删除新鲜事
	 *
	 * @param array $ids ID序列
	 * @return bool
	 */
	public function batchDelete($ids) {
		if (empty($ids) || !is_array($ids)) return false;
		$this->_getDao()->batchDelete($ids);
		$this->_getRelationDao()->batchDelete($ids);
		return true;
	}

	/**
	 * 批量删除某一类型新鲜事
	 *
	 * @param int $type 新鲜事来源类型，必为常量 SEND_* 中的一种
	 * @param int $srcIds ID序列
	 * @return bool
	 */
	public function batchDeleteByType($type, $srcIds) {
		if (!$result = $this->getFreshByType($type, $srcIds)) {
			return false;
		}
		return $this->batchDelete(array_keys($result));
	}
	
	/**
	 * 从用户(A)关注的新鲜事中，删除用户(B)发表的新鲜事
	 *
	 * @param int $uid 用户(A)
	 * @param int $fromuid 用户(B)
	 * @return bool
	 */
	public function deleteAttentionFreshByUid($uid, $fromuid) {
		if (empty($uid) || empty($fromuid)) return false;
		return $this->_getRelationDao()->deleteByUidAndCreatedUid($uid, $fromuid);
	}
	
	/**
	 * 按时间清除一批我关注的新鲜事数据
	 *
	 * @param int $uid 用户id
	 * @param int $limit 清除条数
	 * @return bool
	 */
	public function deleteAttentionFresh($uid, $limit) {
		return $this->_getRelationDao()->deleteOver($uid, $limit);
	}
	
	/**
	 * 批量增加我关注的联系数据
	 *
	 * @param array $data 
	 * @for example :
	 * $data = array(
	 *   0 => array('uid' => ?, 'fresh_id' => ?, 'type' => ?, 'created_userid' => ?, 'created_time' => ?)
	 *	 1 => array()
	 * )
	 * @return bool
	 */
	public function batchAddRelation($data) {
		if (empty($data) || !is_array($data)) return false;
		return $this->_getRelationDao()->batchAdd($data);
	}

	protected function _addRelation($uid, $freshId, $type) {
		$data = array(
			'uid' => $uid,
			'fresh_id' => $freshId,
			'type' => $type,
			'created_userid' => $uid,
			'created_time' => Pw::getTime()
		);
		$this->_getRelationDao()->addRelation($data); //self
		$this->_getRelationDao()->addRelationByAttention($data); //attention
	}


	protected function _getDao() {
		return Wekit::loadDao('attention.dao.PwFreshDao');
	}

	protected function _getRelationDao() {
		return Wekit::loadDao('attention.dao.PwFreshRelationDao');
	}
}