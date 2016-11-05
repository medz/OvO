<?php
Wind::import('LIB:base.PwBaseController');

/**
 * 前台管理面板
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: BaseManageController.php 20606 2012-10-31 14:00:06Z xiaoxia.xuxx $
 * @package wind
 */
class BaseManageController extends PwBaseController {
	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		
		if (!$this->loginUser->isExists()) {
			if ($this->getRequest()->getIsAjaxRequest()) {
				$this->showError('login.not');
			} else {
				$backUrl = WindUrlHelper::createUrl('manage/' . $handlerAdapter->getController().'/'.$handlerAdapter->getAction());
				$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run', array('backurl' => $backUrl)));
			}
		}
		if (!$this->_checkRight()) {
			$this->showError('BBS:manage.thread_check.right.error');
		}
		
		$this->setCurrent($handlerAdapter);
		$this->setLayout('manage_layout');
	}
	
	/**
	 * 设置当前的标签
	 *
	 * @param unknown_type $handlerAdapter
	 */
	protected function setCurrent($handlerAdapter) {
		Wekit::setGlobal($handlerAdapter->getController(), 'manageLeft');
	}

	/**
	 * 判断权限
	 *
	 * @param string $cate
	 * @return boolean
	 */
	private function _checkRight($cate = 'all') {
		/* @var $srv PwPermissionService */
		$srv = Wekit::load('usergroup.srv.PwPermissionService');
		$permission = $srv->getPermissionKeysByCategory('manage_panel');
		$_result = array();
		foreach ($permission as $value) {
			if ($this->loginUser->getPermission($value)) {
				$_result[$value] = true;
			}
		}
		return $cate == 'all' ? $_result : isset($_result[$cate]);
	}
}