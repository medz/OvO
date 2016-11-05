<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:hook.dm.PwHookDm');
/**
 * hook管理
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: ManageController.php 28812 2013-05-24 09:08:16Z jieyin $
 * @package hook
 */
class ManageController extends AdminBaseController {
	private $perpage = 30;
	private $sep = "\r\n";
	/**
	 * hook列表
	 *
	 * @see WindController::run()
	 */
	public function run() {
		$count = $this->_hookDs()->count();
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		list($start, $num) = Pw::page2limit($page, $this->perpage);
		$hooks = $this->_hookDs()->fetchList($num, $start, 'name');
		$this->setOutput(
			array(
				'page' => $page, 
				'perpage' => $this->perpage, 
				'count' => $count, 
				'hooks' => $hooks));
	}

	/**
	 * 展示添加hook页面
	 */
	public function addAction() {
		/* @var $appDs PwApplication */
		$appDs = Wekit::load('APPCENTER:service.PwApplication');
		$apps = $appDs->fetchByPage(0);
		$this->setOutput($apps, 'apps');
	}

	/**
	 * 添加hook
	 */
	public function doAddAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($name, $app, $dec, $param, $interface) = $this->getInput(array('name', 'app', 'dec', 'param', 'interface'), 'post');
		list($appId, $appName) = explode('|', $app);
		$r = $this->_hookDs()->fetchByName($name);
		if ($r) $this->showError(array('HOOK:hook.exit', array('{{error}}' => $name)));
		$dm = new PwHookDm();
		$dm->setAppId($appId);
		$dm->setAppName($appName);
		$dm->setDocument(implode($this->sep, array($dec, $param, $interface)));
		$dm->setName($name);
		$dm->setCreatedTime(Pw::getTime());
		$r = $this->_hookDs()->add($dm);
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		$this->showMessage('success');
	}

	/**
	 * hook编辑展示
	 */
	public function editAction() {
		$name = $this->getInput('name');
		$hook = $this->_hookDs()->fetchByName($name);
		/* @var $appDs PwApplication */
		$appDs = Wekit::load('APPCENTER:service.PwApplication');
		$apps = $appDs->fetchByPage(0);
		$this->setOutput($apps, 'apps');
		$this->setOutput($hook, 'hook');
		
		list($dec, $param, $interface) = explode($this->sep, $hook['document']);
		$this->setOutput(array('dec' => $dec, 'param' => $param, 'interface' => $interface));
	}

	/**
	 * hook编辑
	 */
	public function doEditAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($name, $app, $dec, $param, $interface) = $this->getInput(array('name', 'app', 'dec', 'param', 'interface'), 'post');
		list($appId, $appName) = explode('|', $app);
		$dm = new PwHookDm();
		$dm->setAppId($appId);
		$dm->setAppName($appName);
		$dm->setDocument(implode($this->sep, array($dec, $param, $interface)));
		$dm->setName($name);
		$dm->setModifiedTime(Pw::getTime());
		$r = $this->_hookDs()->update($dm);
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		$this->showMessage('success');
	}

	/**
	 * 删除hook
	 */
	public function delAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$name = $this->getInput('name');
		$r = $this->_hookDs()->delByName($name);
		$r && $r = $this->_injectDs()->delByHookName($name);
		$this->showMessage('success');
	}

	/**
	 * 搜索页
	 */
	public function searchAction() {
		list($name, $app_name) = $this->getInput(array('name', 'app_name'));
		Wind::import('SRV:hook.dm.PwHookSo');
		$so = new PwHookSo();
		$so->setAppName($app_name)->setName($name);
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		list($start, $num) = Pw::page2limit($page, $this->perpage);
		$hooks = $this->_hookDs()->searchHook($so, $num, $start);
		$this->setOutput(
			array(
				'page' => $page, 
				'perpage' => $this->perpage, 
				'name' => $name, 
				'app_name' => $app_name, 
				'hooks' => $hooks, 
				'search' => 1));
		$this->setTemplate('manage_run');
	}

	/**
	 * hook详细页
	 */
	public function detailAction() {
		$name = $this->getInput('name');
		$hook = $this->_hookDs()->fetchByName($name);
		$injectors = $this->_injectDs()->findByHookName($name);
		$this->setOutput(array('hook' => $hook, 'injectors' => $injectors));
		
		list($dec, $param, $interface) = explode($this->sep, $hook['document']);
		$this->setOutput(array('dec' => $dec, 'param' => $param, 'interface' => $interface));
	}
	
	/**
	 *
	 * @return PwHooks
	 */
	private function _hookDs() {
		return Wekit::load('hook.PwHooks');
	}

	/**
	 *
	 * @return PwHookInject
	 */
	private function _injectDs() {
		return Wekit::load('hook.PwHookInject');
	}
}

?>