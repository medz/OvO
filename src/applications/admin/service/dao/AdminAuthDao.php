<?php
Wind::import('ADMIN:library.AdminBaseDao');
/**
 * 用户权限角色表
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminAuthDao.php 21801 2012-12-13 09:31:09Z yishuo $
 * @package admin
 * @subpackage service.dao
 */
class AdminAuthDao extends AdminBaseDao {
	protected $_table = 'admin_auth';
	protected $_dataStruct = array(
		'id', 
		'uid', 
		'username', 
		'roles', 
		'created_time', 
		'modified_time');

	/**
	 * 添加用户权限
	 * 
	 * @param array $fields
	 * @return boolean
	 */
	public function add($fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('INSERT INTO %s SET ') . $this->sqlSingle($fields);
		return $this->getConnection()->execute($sql);
	}

	/**
	 * 更新用户权限设置
	 * 
	 * @param int $id 表主键ID
	 * @param array $auths 用户拥有的权限
	 * @return boolean
	 */
	public function updateById($id, $fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('UPDATE %s SET ') . $this->sqlSingle($fields) . ' WHERE id=?';
		$this->getConnection()->createStatement($sql)->update(array($id));
		return true;
	}

	/**
	 * 删除用户权限设置
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public function del($id) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE id=?');
		$this->getConnection()->createStatement($sql)->update(array($id));
		return true;
	}

	/**
	 * 根据ID查找后台用户
	 *
	 * @param int $id
	 * @return array
	 */
	public function findById($id) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE id=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($id));
	}

	/**
	 * 根据用户ID查找用户后台角色设置
	 *
	 * @param int $uid
	 * @return array
	 */
	public function findByUid($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($uid));
	}

	/**
	 * 根据用户名查找用户权限设置信息
	 *
	 * @param string $username 用户名
	 * @return array
	 */
	public function findByUsername($username) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE username=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($username));
	}

	/**
	 * 获取总的条数
	 * 
	 * @return number
	 */
	public function count() {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s');
		return $this->getConnection()->createStatement($sql)->getValue();
	}

	/**
	 * 分页获取用户权限组列表
	 * 
	 * @param int $start
	 * @param int $count
	 * @return array
	 */
	public function find($start, $count) {
		$sql = $this->_bindTable('SELECT * FROM %s ') . $this->sqlLimit($count, $start);
		return $this->getConnection()->query($sql)->fetchAll();
	}
}

?>