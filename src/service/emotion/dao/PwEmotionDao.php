<?php
Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwEmotionDao.php 6968 2012-03-28 08:53:37Z gao.wanggao $ 
 * @package 
 */

class PwEmotionDao extends PwBaseDao {

 	protected $_pk = 'emotion_id';
 	protected $_table = 'common_emotion';
	protected $_dataStruct = array('emotion_id', 'category_id', 'emotion_name', 'emotion_folder', 'emotion_icon', 'vieworder', 'isused');
 	
	public function getEmotion($emotionId) {
		return $this->_get($emotionId);
	}
	
	public function fetchEmotion($emotionIds){
		return $this->_fetch($emotionIds, 'emotion_id');
	}
	
	public function fetchEmotionByCatid($categoryIds){
		$sql = $this->_bindSql('SELECT * FROM %s WHERE isused = 1 AND category_id IN %s ORDER BY vieworder ASC ', $this->getTable(), $this->sqlImplode($categoryIds));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array());
	}
	
	public function getListByCatid($categoryId, $isUsed = null) {
		$where = 'WHERE category_id =?';
		$_array = array($categoryId);
		if (isset($isUsed)) {
			$where .= ' AND isused = ? ' ;
			$_array[] = $isUsed;
		} 
		$sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY vieworder ASC', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($_array, 'emotion_id');
	}
	
	public function getAllEmotion() {
		$sql = $this->_bindTable('SELECT * FROM %s');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(), 'emotion_id');
	}
	
	public function addEmotion($data) {
		return $this->_add($data);
	}
	
	public function updateEmotion($emotionId, $data) {
		return $this->_update($emotionId, $data);
	}
	
	public function deleteEmotion($emotionId) {
		return $this->_delete($emotionId);
	}

	public function deleteEmotionByCatid($cateId) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE category_id=?');
		$smt = $this->getConnection()->createStatement($sql);
		$smt->update(array($cateId));
		PwSimpleHook::getInstance('PwEmotionDao_deleteEmotionByCatid')->runDo($cateId);
		return true;
	}
 }
?>