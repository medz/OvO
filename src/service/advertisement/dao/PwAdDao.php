<?php
Wind::import('SRC:library.base.PwBaseDao');
/**
 * 
 * 广告位Dao
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z zhudong $
 * @package wind
 */

class PwAdDao extends PwBaseDao {
	
	protected $_table = 'advertisement';
	protected $_pk = 'pid';
	protected $_dataStruct = array('pid','identifier','type_id', 'width', 'height', 'status', 'schedule','show_type','condition');
	
	
	public function getAllAd(){
		$sql = $this->_bindSql('SELECT * FROM %s',$this->getTable());
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('pid');
	}
	
	public function addAdPosition($data){
		if (!$data = $this->_filterStruct($data)) return false;
		$sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	public function editAdPosition($pid,$data){
		if (!$data = $this->_filterStruct($data)) return false;
		$sql = $this->_bindSql('UPDATE %s SET %s  WHERE pid = ? ',$this->getTable(),  $this->sqlSingle($data));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($pid));
	}
	
	public function get($pid) {
		return $this->_get($pid);
	}
	
	public function getByIdentifier($identifier) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE identifier = ? ' ,$this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($identifier));
	}
	
}