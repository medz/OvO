<?php
Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignPageDao.php 18915 2012-10-08 08:21:19Z gao.wanggao $ 
 * @package 
 */
class PwDesignPageDao extends PwBaseDao {
	protected $_pk = 'page_id';
	protected $_table = 'design_page';
	protected $_dataStruct = array('page_id', 'page_type','page_name', 'page_router', 'page_unique', 'is_unique', 'module_ids', 'struct_names' ,'segments', 'design_lock');
	
	public function getPage($id) {
		return $this->_get($id);
	}
	
	public function fetchPage($ids) {
		return $this->_fetch($ids,'page_id');
	}
	
	public function getPageByTypeAndUnique($type, $unique) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `page_type` = ? AND  `page_unique` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($type, $unique));
	}
	
	public function getPageList($type, $offset, $limit) {
		$sqlLimit = $limit ? $this->sqlLimit($limit, $offset) : '';
		$sql = $this->_bindSql ('SELECT * FROM %s WHERE `page_type` & ?  %s', $this->getTable(), $sqlLimit);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($type),'page_id');
	}
	
	public function countPage($type) {
		$sql = $this->_bindSql('SELECT count(*) FROM %s WHERE `page_type` & ? ', $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($type));
	}
	
	public function getPageByRouter($router) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `page_router` = ? ', $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($router));
	}
	
	public function fetchPageByTypeUnique($type, $unique) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `page_type` & ? AND  `page_unique` IN %s', $this->getTable(), $this->sqlImplode($unique));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($type),'page_id');
	}
	
	public function concatModule($value) {
		$value = '%'.$value.'%';
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `module_ids` like ? ', $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($value));
	}
	
	public function addPage($fields) {
		return $this->_add($fields, true);
	}
	
	public function updatePage($id, $fields) {
		return $this->_update($id, $fields);
	}

	public function delete($id) {
		return $this->_delete($id);
	}
	
	public function deleteNoUnique($router, $unique){
		$sql = $this->_bindTable('DELETE FROM %s WHERE  `page_router` = ? AND `is_unique` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($router, $unique));
	}
	
}
?>