<?php
/**
 * 友情链接分类DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 * @package PwLinkTypeDao
 */
class PwLinkTypeDao extends PwBaseDao {
	
	protected $_pk = 'typeid';
	protected $_table = 'link_type';
	protected $_dataStruct = array('typeid','typename','vieworder');
	
	/**
	 * 添加一条分类
	 * 
	 * @param array $data
	 * @return int
	 */
	public function addLinkType($data) {
		return $this->_add($data);
	}
	
	/**
	 * 删除一条分类
	 *
	 * @param int $typeId
	 * @return bool
	 */
	public function delete($typeId) {
		return $this->_delete($typeId);
	}
	
	public function update($typeId,$data) {
		return $this->_update($typeId,$data);
	}
	/**
	 * 修改多条分类
	 *
	 * @param array $data
	 * @return int
	 */
	public function updateLinkTypes($data) {
		foreach ($data as $v) {
			if (!$this->_filterStruct($v) || !$v['typeid']) continue;
			$array[] = array($v['typeid'],$v['vieworder'],$v['typename']);
		}
		if (!$array) return false;
		$sql = $this->_bindTable('REPLACE INTO %s (`typeid`,`vieworder`,`typename`) VALUES ') . $this->sqlMulti($array);
		$this->getConnection()->execute($sql);
		return $this->getConnection()->lastInsertId();
	}
	
	/**
	 * 根据名称获取
	 *
	 * @param string $typename
	 * @return int
	 */
	public function getByName($typename) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `typename`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($typename));
	}
	
	/**
	 * 获取所有分类
	 *
	 * @return int
	 */
	public function getAllTypes() {
		$sql = $this->_bindTable('SELECT * FROM %s ORDER BY `vieworder` ASC');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array(), 'typeid');
	}
	
}