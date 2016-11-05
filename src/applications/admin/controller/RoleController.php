<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 后台角色管理
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-11-12
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: RoleController.php 28783 2013-05-23 09:42:22Z jieyin $
 * @package admin
 * @subpackage controller
 */
class RoleController extends AdminBaseController {

	/**
	 * @see WindController::run()
	 */
	public function run() {
		$roles = $this->_loadRoleService()->findRoles();
		$this->setOutput($roles, 'roles');
	}

	/**
	 * 展示添加角色界面
	 */
	public function addAction() {
		/* @var $menuService AdminMenuService */
		$menuService = Wekit::load('ADMIN:service.srv.AdminMenuService');
		$auths = $menuService->getMenuTable();
		$auths = AdminMenuHelper::resetMenuStruts($auths);
		//remove the admin right setting
		unset($auths['admin']);
		
		$roles = $this->_loadRoleService()->findRoles();
		
		$_tmp = array();
		foreach ($roles as $value)
			$_tmp[$value['name']] = empty($value['auths']) ? array() : explode(',', $value['auths']);
		$this->setOutput($_tmp, 'roleTable');
		$this->setOutput($roles, 'roles');
		$this->setOutput($auths, 'auths');
	}

	/**
	 * 添加角色
	 */
	public function doAddAction() {
		list($rolename, $auths) = $this->getInput(array('rolename', 'auths'), 'post');
		$result = $this->_loadRoleService()->addRole($rolename, $auths);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage('AdMIN:role.add.success', 'role/run');
	}

	/**
	 * 编辑界面展示
	 */
	public function editAction() {
		$rid = $this->getInput('rid');
		$role = $this->_loadRoleService()->findRoleById($rid);
		if ($role instanceof PwError) $this->showError($role->getError());
		
		/* @var $menuService AdminMenuService */
		$menuService = Wekit::load('ADMIN:service.srv.AdminMenuService');
		$auths = $menuService->getMenuTable();
		$auths = AdminMenuHelper::resetMenuStruts($auths);
		//remove the admin right setting
		unset($auths['admin']);
		
		$roles = $this->_loadRoleService()->findRoles();
		$_tmp = array();
		foreach ($roles as $value)
			$_tmp[$value['name']] = empty($value['auths']) ? array() : explode(',', $value['auths']);
		$_cAuths = $_tmp[$role['name']];
		
		$this->setOutput($_tmp, 'roleTable');
		$this->setOutput($roles, 'roles');
		$this->setOutput($_cAuths, 'cAuths');
		$this->setOutput($role, 'role');
		$this->setOutput($auths, 'auths');
	}

	/**
	 * 编辑角色
	 */
	public function doEditAction() {
		list($rid, $rolename, $auths) = $this->getInput(array('rid', 'rolename', 'auths'), 'post');
		$result = $this->_loadRoleService()->editRole($rid, $rolename, $auths);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage('ADMIN:role.edit.success');
	}

	/**
	 * 删除角色
	 */
	public function delAction() {
		$rid = $this->getInput('rid', 'post');
		!$rid && $this->showError('operate.fail');

		$result = $this->_loadRoleService()->delById($rid);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage('ADMIN:role.del.success');
	}

	/**
	 * 加载角色管理服务
	 *
	 * @return AdminRole
	 */
	private function _loadRoleService() {
		return Wekit::load('ADMIN:service.AdminRole');
	}
}

?>