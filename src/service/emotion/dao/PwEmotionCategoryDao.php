<?php
Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwEmotionCategory.php 6791 2012-03-26 08:25:51Z gao.wanggao $ 
 * @package 
 */

 class PwEmotionCategoryDao extends PwBaseDao {

 	protected $_pk = 'category_id';
 	protected $_table = 'common_emotion_category';
	protected $_dataStruct = array('category_id', 'category_name', 'emotion_folder', 'emotion_apps', 'orderid', 'isopen');
 	
	public function getCategory($categoryId) {
		return $this->_get($categoryId);
	}
	
	public function fetchCategory($categoryIds){
		return $this->_fetch($categoryIds);
	}
	
	public function getCategoryList($app, $isOpen = null) {
		$where = 'WHERE 1';
		$_array = array();
		if ($app) {
			$where .= ' AND emotion_apps like ?' ;
			$_array[] = '%'.$app.'%';
		}
		if (isset($isOpen)) {
			$where .= ' AND isopen =?' ;
			$_array[] = $isOpen;
		} 
		$sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY orderid ASC', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($_array, 'category_id');
	}
	
 	public function addCategory($data) {
		return $this->_add($data);
	}
	
	public function updateCategory($categoryId, $data) {
		return $this->_update($categoryId, $data);
	}
	
	public function deleteCategory($categoryId) {
		return $this->_delete($categoryId);
	}
 }
?>