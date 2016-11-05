<?php

/**
 * 标签和用户的关系表
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserTagRelationDao.php 10043 2012-05-16 04:51:07Z jinlong.panjl $
 * @package src.service.usertag.dao
 */
class PwUserTagRelationDao extends PwBaseDao {
	protected $_table = 'user_tag_relation';
	protected $_dataStruct = array('uid', 'tag_id', 'created_time');
	
	/**
	 * 根据用户ID获得用户的标签关系
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @return array
	 */
	public function getRelationByUidAndTagid($uid, $tag_id) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` = ? AND `tag_id` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid, $tag_id));
	}
	
	/**
	 * 根据用户ID获得用户的标签关系
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getRelationByUid($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'tag_id');
	}

	/**
	 * 根据用户ID统计该用户已经拥有的标签数组
	 *
	 * @param int $uid
	 * @return array
	 */
	public function countByUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}
	
	/**
	 * 根据标签ID获得该标签的相关用户记录数量
	 *
	 * @param int $tag_id
	 * @return array
	 */
	public function countRelationByTagid($tag_id) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `tag_id` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($tag_id));
	}
	
	/**
	 * 根据标签ID获得该标签的相关用户记录
	 *
	 * @param int $tag_id
	 * @return array
	 */
	public function getRelationByTagid($tag_id, $limit, $start) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_id` = ? %s', $this->getTable(), $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($tag_id), 'uid');
	}
	
	/**
	 * 删除关系
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @return boolean
	 */
	public function deleteRelation($uid, $tag_id) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` = ? AND `tag_id` = ?');
		$result = $this->getConnection()->createStatement($sql)->execute(array($uid, $tag_id), true);
		if ($result) {
			PwSimpleHook::getInstance('PwUserTagRelationDao_deleteRelation')->runDo($tag_id, array(), array('used_count' => -1));
		}
		return $result;
	}
	
	/**
	 * 批量删除关系
	 *
	 * @param int $uid
	 * @param array $tag_ids
	 * @return boolean
	 */
	public function batchDeleteRelation($uid, $tag_ids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` = ? AND `tag_id` IN %s', $this->getTable(), $this->sqlImplode($tag_ids));
		return $this->getConnection()->createStatement($sql)->execute(array($uid), true);
	}
	
	/**
	 * 根据标签ID列表批量删除标签关系
	 *
	 * @param array $tag_ids
	 */
	public function batchDeleteRelationByTagids($tag_ids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `tag_id` IN %s', $this->getTable(), $this->sqlImplode($tag_ids));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据用户ID删除该用户的关系
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function deleteRelationByUid($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($uid));
	}
	
	/**
	 * 根据用户ID列表批量删除数据
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function batchDeleteRelationByUids($uids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` IN %s', $this->getTable(), $this->sqlImplode($uids));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 添加关系
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @param int $created_time
	 * @return boolean
	 */
	public function addRelation($uid, $tag_id, $created_time) {
		$sql = $this->_bindTable('REPLACE INTO %s (`uid`, `tag_id`, `created_time`) VALUES (?, ?, ?)');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($uid, $tag_id, $created_time));
	}
}