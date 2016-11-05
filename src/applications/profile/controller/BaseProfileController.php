<?php
Wind::import('APPS:u.service.helper.PwUserHelper');

/**
 * 左边导航和资料tab扩展
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: BaseProfileController.php 22678 2012-12-26 09:22:23Z jieyin $
 * @package src.products.u.controller.profile
 */
class BaseProfileController extends PwBaseController {
	
	protected $defaultGroups = array(
			0 => array('name' => '普通组', 'gid' => '0'), 
		);
	protected $bread = array();

	/* (non-PHPdoc)
	 * @see PwBaseController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run', array('_type' => $this->getInput('_type'))));
		}
		if (!$this->getRequest()->getIsAjaxRequest()) {
			$this->setLayout('TPL:profile.profile_layout');
		}
	}
	
	/**
	 * 获得个人中心菜单服务
	 *
	 * @return PwUserProfileMenu
	 */
	protected function getMenuService() {
		return Wekit::load('APPS:profile.service.PwUserProfileMenu');
	}
	
	/** 
	 * 设置当前设置项
	 * 
	 * @param string $left
	 */
	protected function setCurrentLeft($left = '', $tab = '') {
		$menus = $this->getMenuService()->getMenus();
		$left = $left ? $left : $this->getInput('_left');
		$tab = $tab ? $tab : $this->getInput('_tab');
		list($left, $tab) = $this->getMenuService()->getCurrentTab($left, $tab);
		$currentMenu = $menus[$left];
		$tab && $currentMenu = $currentMenu['tabs'][$tab];
		if (!isset($currentMenu['url'])) {
			$this->forwardRedirect(WindUrlHelper::createUrl('profile/extends/run', array('_left' => $left, '_tab' => $tab)));
		}
		
		$menus[$left]['current'] = 'current';
		$this->bread['left'] = array('url' => WindUrlHelper::createUrl($menus[$left]['url'], array('_left' => $left)), 'title' => $menus[$left]['title']);
		Wekit::setGlobal($menus, 'profileLeft');
		
		if ($menus[$left]['tabs']) {
			$menus[$left]['tabs'][$tab]['current'] = 'current';
			$this->appendBread($menus[$left]['tabs'][$tab]['title'], WindUrlHelper::createUrl($menus[$left]['tabs'][$tab]['url'], array('_tab' => $tab, '_left' => $left)));
			$this->setOutput($menus[$left]['tabs'], '_tabs');
		}
	}
	
	/**
	 * 设置面包屑
	 *
	 * @param string $title
	 * @param string $url
	 */
	protected function appendBread($title, $url) {
		$this->bread[] = array('url' => $url, 'title' => $title);
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseController::afterAction()
	 */
	public function afterAction($handlerAdapter) {
		parent::afterAction($handlerAdapter);
		$bread = array($this->bread['left']);
		unset($this->bread['left']);
		$this->bread && $bread = array_merge($bread, $this->bread);
		Wekit::setGlobal($bread, 'profileBread');
	}
}