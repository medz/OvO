<?php
/**
 * 热门话题榜数据DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 * @package PwTagRecordDao
 */
class PwTagRecordDao extends PwBaseDao {
	
	protected $_table = 'tag_record';
	protected $_table_category_relation = 'tag_category_relation';
	protected $_dataStruct = array('tag_id','is_reply','update_time');
	
	/**
	 * 添加
	 * 
	 * @param int $tagId
	 * @param int $updateTime 
	 */
	public function addTagRecord($data) {
		return $this->_add($data);
	}
	
	/**
	 * 更新tag update表的tagid
	 * 
	 * @param int $fromTagId
	 * @param int $toTagId
	 * @return bool
	 */
	public function updateTagRecordByTagId($fromTagId,$toTagId){
		$sql = $this->_bindTable('UPDATE %s SET tag_id=? WHERE `tag_id`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($toTagId,$fromTagId));
	}
	
	/**
	 * 批量添加
	 * 
	 * @param array $data
	 * @return int
	 */
	public function batchAddTagRecord($data) {
		$array = array();
		foreach ($data as $v) {
			if (!$this->_filterStruct($v)) continue;
			$array[] = array(
				$v['tag_id'],	
				intval($v['is_reply']),
				$v['update_time'],	
			);
		}
		$sql = $this->_bindSql('INSERT INTO %s (`tag_id`,`is_reply`,`update_time`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据tag_id删除
	 *
	 * @param int $tagId
	 * @return bool
	 */
	public function deleteByTagId($tagId) {
		$sql = $this->_bindTable('DELETE FROM %s  WHERE `tag_id`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($tagId));
	}
	
	/**
	 * 根据tag_ids批量删除
	 *
	 * @param array $tagIds
	 * @return bool
	 */
	public function deleteByTagIds($tagIds) {
		$sql = $this->_bindSql('DELETE FROM %s  WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据时间删除
	 *
	 * @param int $updateTime
	 * @return bool
	 */
	public function deleteByTime($updateTime) {
		$sql = $this->_bindTable('DELETE FROM %s  WHERE `update_time` <?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($updateTime));
	}
	
	/**
	 * 统计热门话题榜
	 *
	 * @param int $num
	 * @return array
	 */
	public function getHotTags($num) {
		$sql = $this->_bindSql('SELECT `tag_id`,COUNT(*) AS cnt FROM %s GROUP BY `tag_id` ORDER BY cnt DESC %s ', $this->getTable(),  $this->sqlLimit($num));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(),'tag_id');
	}
	
	/**
	 * 根据话题分类统计热门话题榜
	 *
	 * @param int $categoryId
	 * @param int $num
	 * @return array
	 */
	public function getHotTagsByCategory($categoryId,$num) {
		$sql = $this->_bindTable('SELECT `tag_id`,COUNT(*) AS cnt FROM %s GROUP BY `tag_id` ');
		$sql = $this->_bindSql('SELECT * FROM (%s) AS t1 LEFT JOIN %s AS t2 USING (tag_id) WHERE t2.`category_id` =? ORDER BY t1.`cnt` DESC '. $this->sqlLimit($num),$sql,$this->getTable($this->_table_category_relation));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($categoryId),'tag_id');
	}
}