<?php
Wind::import('ADMIN:library.AdminBaseDao');
/**
 * 用户文件管理
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminRoleDao.php 21801 2012-12-13 09:31:09Z yishuo $
 * @package admin
 * @subpackage service.dao
 */
class AdminRoleDao extends PwBaseDao {
	protected $_table = 'admin_role';
	protected $_dataStruct = array('id', 'name', 'auths', 'created_time', 'modified_time');

	/**
	 * 添加后台角色
	 *
	 * @param array $fields
	 * @return boolean|number
	 */
	public function add($fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('INSERT INTO %s SET ') . $this->sqlSingle($fields);
		return $this->getConnection()->execute($sql);
	}

	/**
	 * 删除后台角色设置
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function del($id) {
		if (!$id) return false;
		$sql = $this->_bindTable('DELETE FROM %s WHERE id=?');
		$this->getConnection()->createStatement($sql)->execute(array($id));
		return true;
	}

	/**
	 * 更新角色定义
	 *
	 * @param array $fields
	 * @return boolean
	 */
	public function updateById($id, $fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('UPDATE %s SET ') . $this->sqlSingle($fields) . ' WHERE id=?';
		$this->getConnection()->createStatement($sql)->update(array($id));
		return true;
	}

	/**
	 * 分页查找用户角色,并返回结果列表
	 *
	 * @param int $start
	 * @param int $count
	 * @return array
	 */
	public function find($start, $count) {
		$sql = $this->_bindTable('SELECT * FROM %s ') . $this->sqlLimit($start, $count);
		return $this->getConnection()->query($sql)->fetchAll();
	}

	/**
	 * 根据主键name查找数据
	 *
	 * @param array $names
	 * @return array
	 */
	public function findByNames($names) {
		if (!$names) return false;
		$sql = $this->_bindTable('SELECT * FROM %s WHERE name IN ') . $this->sqlImplode($names);
		return $this->getConnection()->createStatement($sql)->queryAll();
	}

	/**
	 * 根据角色名称查找一条数据
	 *
	 * @param string $name
	 * @return array
	 */
	public function findByName($name) {
		if (!$name) return false;
		$sql = $this->_bindTable('SELECT * FROM %s WHERE name=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($name));
	}

	/**
	 * 根据主键ID查找数据
	 *
	 * @param array $ids
	 * @return array
	 */
	public function findByIds($ids) {
		if (!$ids) return false;
		$sql = $this->_bindTable('SELECT * FROM %s WHERE id IN ') . $this->sqlImplode($ids);
		return $this->getConnection()->createStatement($sql)->queryAll();
	}

	/**
	 * 根据主键ID查找一条数据
	 *
	 * @param int $id
	 * @return array
	 */
	public function findById($id) {
		if (!$id) return false;
		$sql = $this->_bindTable('SELECT * FROM %s WHERE id=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($id));
	}
}

?>