<?php
Wind::import('SRC:library.base.PwBaseDao');

/**
 * 搜索记录
 */
class App_SearchDao extends PwBaseDao 
{
	
	protected $_table = 'app_search';
    protected $_pk = 'keywords';
	protected $_dataStruct = array('keywords', 'search_type', 'num');
	
	/**
	 * 获取信息
	 *
	 * @param int $id
	 * @return array
	 */
	public function get($keywords, $type)
	{
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `search_type`=? AND `keywords`=? ');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($type, $keywords));
	}
	
	/**
	 * 单条添加
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add($data) 
	{
		return $this->_add($data);
	}
	
	/**
	 * 单条删除
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteByTypeAndKey($keywords, $type) 
	{
		$sql = $this->_bindTable('DELETE FROM %s WHERE `keywords`=? AND `search_type`=?');
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($keywords, $type));
	}
	
	/**
	 * 根据TYPE获取num条数据
	 * 
	 * @param int $num
	 * @return array 
	 */
	public function getByAndType($type, $num) 
	{
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `search_type`=? order by num desc limit 0,?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($type, $num));
	}
	/**
	 * 根据类型删除
	 *
	 * @param int $uid
	 * @param int $type
	 * @return bool 
	 */
	public function deleteByUidAndType($type) 
	{
		$sql = $this->_bindTable('DELETE FROM %s WHERE `search_type`=?');
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($type));
	}
	
	
	/**
	 * 单条修改
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($keywords, $type, $fields, $increaseFields = array(), $bitFields = array()) 
	{
		$fields = $this->_filterStruct($fields);
		$increaseFields = $this->_filterStruct($increaseFields);
		$bitFields = $this->_filterStruct($bitFields);
		if (!$fields && !$increaseFields && !$bitFields) {
			return false;
		}
		$sql = $this->_bindSql('UPDATE %s SET %s WHERE %s=? and search_type=?', $this->getTable(), $this->sqlMerge($fields, $increaseFields, $bitFields), $this->_pk);
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($keywords, $type));
		PwSimpleHook::getInstance($this->_class() . '_update')->runDo($id, $fields, $increaseFields);
		return $result;
	}
}