<?php
Wind::import('ADMIN:library.AdminBaseDao');
/**
 * 常用菜单数据访问层
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminCustomDao.php 21801 2012-12-13 09:31:09Z yishuo $
 * @package admin
 * @subpackage service
 */
class AdminCustomDao extends AdminBaseDao {
	protected $_table = 'admin_custom';
	protected $_dataStruct = array('username', 'custom');

	/**
	 * 添加或修改常用菜单
	 * 
	 * @param int $uid
	 * @param string $custom
	 * 
	 * @return boolean
	 */
	public function replace($username, $custom) {
		$sql = $this->_bindTable('REPLACE INTO %s SET `username` = ?, `custom` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($username, $custom));
	}

	/**
	 * 根据用户名查找用户常用菜单
	 *
	 * @param int $username
	 * @return array
	 */
	public function findByUsername($username) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE username=?');
		return $this->getConnection()->createStatement($sql)->getOne(array($username));
	}
}

?>