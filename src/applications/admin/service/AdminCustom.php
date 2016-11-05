<?php
/**
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AdminCustom.php 16114 2012-08-20 05:41:56Z long.shi $
 * @package admin
 * @subpackage service
 */
class AdminCustom {

	/**
	 * 根据用户名查找用户常用菜单
	 *
	 * @param string $username
	 * @return array|PwError
	 */
	public function findByUsername($name) {
		return $this->loadCustomDao()->findByUsername($name);
	}

	/**
	 * 添加或修改常用菜单
	 * 
	 * @param string $username
	 * @param string $custom
	 * 
	 * @return boolean
	 */
	public function replace($username, $custom) {
		if (!$username) return new PwError('ADMIN:custom.replace.fail');
		return $this->loadCustomDao()->replace($username, $custom);
	}

	/**
	 * @return AdminCustomDao
	 */
	private function loadCustomDao() {
		return Wekit::loadDao('ADMIN:service.dao.AdminCustomDao');
	}
}

?>