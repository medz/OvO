<?php
/**
 * 本地应用管理，dao层
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwApplicationDao.php 23963 2013-01-17 08:40:39Z long.shi $
 * @package products
 * @subpackage appcenter.service.dao
 */
class PwApplicationDao extends PwBaseDao {
	protected $_table = 'application';
	protected $_pk = 'app_id';
	protected $_dataStruct = array(
		'app_id', 
		'name', 
		'alias', 
		'logo', 
		'status', 
		'author_name', 
		'author_icon', 
		'author_email', 
		'website', 
		'version', 
		'pwversion', 
		'created_time', 
		'modified_time', 
		'description');

	/**
	 * 添加应用
	 *
	 * @param array $fields        	
	 * @return boolean
	 */
	public function add($fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('INSERT INTO %s SET ') . $this->sqlSingle($fields);
		$statement = $this->getConnection()->createStatement($sql);
		$statement->execute();
		return $this->getConnection()->lastInsertId('app_id');
	}

	/**
	 * 根据appid刪除应用，返回影响行数
	 *
	 * @param string $id        	
	 * @return int
	 */
	public function delByAppId($id) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE app_id=?');
		return $this->getConnection()->createStatement($sql)->execute(array($id));
	}

	/**
	 * 更新应用，返回影响行数
	 *
	 * @param string $app_id        	
	 * @param array $fields        	
	 * @return int
	 */
	public function update($app_id, $fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('UPDATE %s set ') . $this->sqlSingle($fields) . ' WHERE app_id=?';
		return $this->getConnection()->createStatement($sql)->execute(array($app_id));
	}

	/**
	 * 根据ID查找App应用注册信息，返回app数据
	 *
	 * @param string $appId        	
	 * @return array
	 */
	public function findByAppId($appId) {
		if (!$appId) return false;
		$sql = $this->_bindTable('SELECT * FROM %s ') . ' WHERE app_id=?';
		return $this->getConnection()->createStatement($sql)->getOne(array($appId));
	}

	/**
	 * 根据应用别名查找应用注册信息，返回app数据
	 *
	 * @param string $alias        	
	 * @return array
	 */
	public function findByAlias($alias) {
		if (!$alias) return false;
		$sql = $this->_bindTable('SELECT * FROM %s ') . ' WHERE alias=?';
		return $this->getConnection()->createStatement($sql)->getOne(array($alias));
	}
	
	/**
	 * 根据应用别名查找应用注册信息，返回app数据
	 *
	 * @param array $alias
	 * @return array
	 */
	public function fetchByAlias($alias, $index = 'app_id') {
		if (!$alias) return array();
		$sql = $this->_bindSql('SELECT * FROM %s WHERE alias IN %s', $this->getTable(), $this->sqlImplode($alias));
		return $this->getConnection()->query($sql)->fetchAll($index);
	}

	/**
	 * 根据app_id批量获取
	 *
	 * @param array $ids        	
	 * @return array
	 */
	public function fetchByAppId($ids, $index = 'app_id') {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE app_id IN %s', $this->getTable(), 
			$this->sqlImplode($ids));
		return $this->getConnection()->query($sql)->fetchAll($index);
	}

	/**
	 * app列表
	 *
	 * @param int $num        	
	 * @param int $start        	
	 * @return array
	 */
	public function fetchByPage($num = 10, $start = 0, $index = 'app_id') {
		$sql = $this->_bindSql('SELECT * FROM %s ORDER BY `created_time` DESC %s', 
			$this->getTable(), $this->sqlLimit($num, $start));
		return $this->getConnection()->createStatement($sql)->queryAll(array(), $index);
	}

	/**
	 * 根据status获取列表
	 *
	 * @param int $num
	 * @param int $start
	 * @param int $status 是否有独立页面
	 * @param string $orderby
	 * @return array
	 */
	public function fetchListByStatus($num = 10, $start = 0, $status = 1, $orderby = 'created_time') {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE status & ? ORDER BY ? DESC %s', 
			$this->getTable(), $this->sqlLimit($num, $start));
		return $this->getConnection()->createStatement($sql)->queryAll(array($status, $orderby), $this->_pk);
	}
	
	/**
	 * 根据status获取总数
	 *
	 * @param int $status
	 * @return int
	 */
	public function countByStatus($status = 1) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE status & ?');
		return $this->getConnection()->createStatement($sql)->getValue(array($status));
	}

	/**
	 * 根据应用名称模糊搜索
	 *
	 * @param string $name        	
	 * @param int $num        	
	 * @param int $start        	
	 * @return array
	 */
	public function searchByName($name, $num = 10, $start = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `name` LIKE ? ORDER BY `created_time` %s', 
			$this->getTable(), $this->sqlLimit($num, $start));
		return $this->getConnection()->createStatement($sql)->queryAll(array("%$name%"), $this->_pk);
	}

	/**
	 * 统计搜索结果
	 *
	 * @param string $name        	
	 * @return int
	 */
	public function countSearchByName($name) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `name` LIKE ?');
		return $this->getConnection()->createStatement($sql)->getValue(array("%$name%"));
	}

	/**
	 * 获取app总数
	 *
	 * @return int
	 */
	public function count() {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s');
		return $this->getConnection()->createStatement($sql)->getValue();
	}
}

?>