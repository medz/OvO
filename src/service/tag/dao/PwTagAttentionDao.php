<?php
/**
 * 我关注的话题dao
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package PwTagAttentionDao
 */
class PwTagAttentionDao extends PwBaseDao {

	protected $_table = 'tag_attention';
	protected $_dataStruct = array('uid', 'tag_id', 'last_read_time');
	
	/**
	 * 根据uid和tagId获取话题
	 *
	 * @param int $uid
	 * @param int $tagId
	 * @return array 
	 */
	public function get($uid, $tagId) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `tag_id`=? AND `uid`=?');  
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($tagId, $uid));
	}

	/**
	 * 统计我关注的话题
	 *
	 * @param int $uid
	 * @return int 
	 */
	public function countByUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `uid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}

	/**
	 * 取我关注的热门话题
	 *
	 * @param int $uid
	 * @param array $tagIds
	 * @return int 
	 */
	public function getAttentionByUidAndTagsIds($uid,$tagIds){
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_id` IN %s AND `uid`=?', $this->getTable(),$this->sqlImplode($tagIds));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid),'tag_id');
		
	}
	
	/**
	 * 获取我关注的话题
	 *
	 * @param int $uid
	 * @param int $start
	 * @param int $limit
	 * @return array 
	 */
	public function getByUid($uid,$limit = 100,$start = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `uid`=? ORDER BY `last_read_time` DESC %s ', $this->getTable(), $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid),'tag_id');
	}

	/**
	 * 统计关注话题的用户
	 *
	 * @param int $tagId
	 * @return array 
	 */
	public function countByTagId($tagId) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `tag_id`=? ');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($tagId));
	}
	
	/**
	 * 获取关注话题的用户
	 *
	 * @param int $tagId
	 * @param int $start
	 * @param int $limit
	 * @return array 
	 */
	public function getByTagId($tagId,$start,$limit) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_id`=? %s', $this->getTable(), $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($tagId),'uid');
	}
	
	/**
	 * 添加一条关注
	 *
	 * @param array $data
	 * @return int
	 */
	public function add($data) {
		if (!$data = $this->_filterStruct($data)) {
			return false;
		}
		$sql = $this->_bindSql('REPLACE INTO %s SET %s ', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 删除一条关注
	 *
	 * @param int $uid
	 * @param int $tagId
	 * @return int
	 */
	public function delete($uid, $tagId) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `tag_id`=? AND `uid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($tagId, $uid));
	}
	
	/**
	 * 根据tag_ids删除
	 *
	 * @param array $tagIds
	 * @return bool
	 */
	public function deleteByTagIds($tagIds) {
		$sql = $this->_bindSql('DELETE FROM %s  WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
		return $this->getConnection()->execute($sql);
	}
}