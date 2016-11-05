<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:hook.dm.PwHookInjectDm');
/**
 * inject
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: InjectController.php 28812 2013-05-24 09:08:16Z jieyin $
 * @package hook.admin
 */
class InjectController extends AdminBaseController {

	/**
	 * 添加inject展示页
	 */
	public function addAction() {
		$hook_name = $this->getInput('hook_name');
		$hooks = $this->_hookDs()->fetchList(0);
		$this->setOutput($hook_name, 'hook_name');
		$this->setOutput($hooks, 'hooks');
	}

	/**
	 * 添加inject
	 */
	public function doAddAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($alias, $hook_name, $class, $method, $loadway, $expression, $description) = $this->getInput(
			array('alias', 'hook_name', 'class', 'method', 'loadway', 'expression', 'description'));
		$dm = new PwHookInjectDm();
		$dm->setAlias($alias)->setHookName($hook_name)->setClass($class)->setMethod($method)->setLoadWay(
			$loadway)->setExpression($expression)->setDescription($description);
		$r = $this->_injectDs()->add($dm);
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		$this->showMessage('success');
	}

	/**
	 * 编辑inject展示
	 */
	public function editAction() {
		$id = $this->getInput('id');
		$inject = $this->_injectDs()->find($id);
		$hooks = $this->_hookDs()->fetchList(0);
		$this->setOutput($hooks, 'hooks');
		$this->setOutput($inject, 'inject');
	}

	/**
	 * 编辑inject
	 */
	public function doEditAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($id, $alias, $hook_name, $class, $method, $loadway, $expression, $description) = $this->getInput(
			array(
				'id', 
				'alias', 
				'hook_name', 
				'class', 
				'method', 
				'loadway', 
				'expression', 
				'description'));
		$dm = new PwHookInjectDm();
		$dm->setId($id)->setAlias($alias)->setHookName($hook_name)->setClass($class)->setMethod(
			$method)->setLoadWay($loadway)->setExpression($expression)->setDescription($description);
		$r = $this->_injectDs()->update($dm);
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		$this->showMessage('success');
	}

	/**
	 * 删除injector
	 */
	public function delAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$id = $this->getInput('id');
		$this->_injectDs()->del($id);
		$this->showMessage('success');
	}

	/**
	 * injector详细页
	 */
	public function detailAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$id = $this->getInput('id');
		$inject = $this->_injectDs()->find($id);
		$this->setOutput($inject, 'inject');
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