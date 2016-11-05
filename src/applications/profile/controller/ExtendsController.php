<?php
Wind::import('SRV:user.validator.PwUserValidator');
Wind::import('SRV:user.PwUserBan');
Wind::import('APPS:profile.service.PwUserProfileExtends');
		
/**
 * 用户资料页面
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: ExtendsController.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package src.products.u.controller.profile
 */
class ExtendsController extends PwBaseController {
	
	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run', array('_type' => $this->getInput('_type'))));
		}
	}
    
    /* (non-PHPdoc)
	 * @see PwBaseController::run()
	 */
	public function run() {
		/* @var $profileMenuSrv PwUserProfileMenu */
		$profileMenuSrv = Wekit::load('APPS:profile.service.PwUserProfileMenu');
		list($_left, $_tab) = $profileMenuSrv->getCurrentTab($this->getInput('_left'), $this->getInput('_tab'));
		$menus = $profileMenuSrv->getMenus();
		$currentMenu = $menus[$_left];
		($_tab) && $currentMenu = $menus[$_left]['tabs'][$_tab];
		if (!$currentMenu) $this->showError('USER:profile.extends.noexists');
		
		$extendsSrv = new PwUserProfileExtends($this->loginUser);
		$extendsSrv->setCurrent($_left, $_tab);
		$this->runHook('c_profile_extends_run', $extendsSrv);
		$this->setOutput($extendsSrv, 'hookSrc');
		$this->setOutput($menus, '_menus');
		$this->setOutput($_left, '_left');
		$this->setOutput($_tab, '_tab');
		$this->setTemplate('extends_run');
	}
	
	/**
	 * 接受表单处理
	 */
	public function dorunAction() {
		/* @var $profileMenuSrv PwUserProfileMenu */
		$profileMenuSrv = Wekit::load('APPS:profile.service.PwUserProfileMenu');
		list($_left, $_tab) = $profileMenuSrv->getCurrentTab($this->getInput('_left'), $this->getInput('_tab'));
		$menus = $profileMenuSrv->getMenus();
		$currentMenu = $menus[$_left];
		($_tab) && $currentMenu = $menus[$_left]['tabs'][$_tab];
		if (!$currentMenu) $this->showError('USER:profile.extends.noexists');
		
		$extendsSrv = new PwUserProfileExtends($this->loginUser);
		$extendsSrv->setCurrent($_left, $_tab);
		$this->runHook('c_profile_extends_dorun', $extendsSrv);
		$r = $extendsSrv->execute();
		if ($r instanceof PwError) {
			$this->showError($r->getError());
		}
		$this->showMessage('success');
	}
}