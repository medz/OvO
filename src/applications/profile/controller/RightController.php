<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
Wind::import('SRV:usergroup.srv.PwPermissionService');
Wind::import('SRV:user.dm.PwUserInfoDm');
/**
 * 用户权限相关
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: RightController.php 23994 2013-01-18 03:51:46Z long.shi $
 * @package src.products.u.controller.profile
 */
class RightController extends BaseProfileController {
	private $banGid = array(1, 2, 6, 7);
	
	/* (non-PHPdoc)
	 * @see BaseProfileController::beforeAction()
	 */
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setCurrentLeft('right');
	}
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$permissionService = new PwPermissionService();
		$categorys = $permissionService->getPermissionPoint($this->_getShowPoint(), array('basic', 'bbs'));
		$compare = $this->getInput('gid');
		if ($compare && $compare != $this->loginUser->gid) {
			$this->setOutput(true, 'compare');
			$compareGroup = $permissionService->getPermissionConfigByGid($compare, $this->_getShowPoint());
			$this->setOutput($compareGroup, 'compareGroupPermission');
			$this->setOutput($compare, 'comparegid');
		}
		$myGroup = $permissionService->getPermissionConfigByGid($this->loginUser->gid, $this->_getShowPoint());
		$this->listGroups();
		$attach = array('allow_upload', 'allow_download', 'uploads_perday'/*, 'upload_file_types'*/);
		foreach ($categorys['bbs']['sub'] as $_k => $_v) {
			if (!in_array($_v, $attach)) continue;
			unset($categorys['bbs']['sub'][$_k]);
		}
		$totalCredit = Wekit::load('usergroup.srv.PwUserGroupsService')->getCredit($this->loginUser->info);
		$categorys['attach'] = array('name' => '附件权限', 'sub' => $attach);
		$this->setOutput($categorys, 'categorys');
		$this->setOutput($myGroup, 'myGroupPermission');
		$this->setOutput($totalCredit, 'myCredit');
		$this->_appendBread('权限查看', WindUrlHelper::createUrl('profile/right/run'));
		$this->setTemplate('profile_right');
		
		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:profile.right.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}

	/**
	 * 设置用户组
	 */
	public function dosetAction() {
		$gid = $this->getInput('gid', 'post');
		if (!$gid) $this->showError('USER:right.gid.require');
		if (in_array($this->loginUser->gid, $this->banGid)) $this->showError('USER:update.error');
		/* @var $belongDs PwUserBelong */
		$belongDs = Wekit::load('user.PwUserBelong');
		$belongs = $belongDs->getUserBelongs($this->loginUser->uid);
		$_groups = array();
		$time = Pw::getTime();
		foreach ($belongs as $_item) {
			if ($_item['endtime'] == 0 || $_item['endtime'] > $time) {
				$_groups[$_item['gid']] = $_item['endtime'];
			}
		}
		
		//普通组不能作为当前组，如果拥有拥有附加组的话,当前组必定产生于附加用户组
		if (!$_groups || !in_array($gid, array_keys($_groups))) $gid = 0;
		if ($gid == 0) {
			/* @var $userService PwUserService */
			$userService = Wekit::load('user.srv.PwUserService');
			list($gid, $_groups) = $userService->caculateUserGroupid($gid, $_groups);
		}
		
		$dm = new PwUserInfoDm($this->loginUser->uid);
		$dm->setGroupid($gid)->setGroups($_groups);
		
		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$result = $userDs->editUser($dm, PwUser::FETCH_MAIN);
		if ($result instanceof PwError) $this->showError($result->getError());
		$this->showMessage('USER:update.success', 'profile/right/run');
	}

	/**
	 * 获取显示相关用户组的信息
	 */
	private function listGroups() {
		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');
		$groups = $groupDs->getTypeNames();
		$groupsType = $switchGroups = $myGroups = array();
		$allGroups = $groupDs->getAllGroups();
		foreach ($allGroups as $gid => $_item) {
			$groupsType[$_item['type']]['name'] = $groups[$_item['type']];
			$groupsType[$_item['type']]['sub'][$gid] = $_item;
		}
		if (in_array($this->loginUser->gid, $this->banGid)) {
			$myGroups = array($this->loginUser->gid);
			$switchGroups = array();
		} else {
			foreach ($this->loginUser->groups as $value) {
				if (!$value || $value == $this->loginUser->info['memberid']) continue;
				$switchGroups[] = $value;
			}
			$myGroups = array_merge($this->loginUser->groups, array($this->loginUser->info['memberid']));
			$myGroups = array_unique($myGroups);
		}
		$this->setOutput(array('member', 'special', 'system'), 'showTypes');
		$this->setOutput($allGroups, 'allGroups');
		$this->setOutput($groupsType, 'groupTypes');
		$this->setOutput($myGroups, 'myGroups');
		$this->setOutput($switchGroups, 'switchGroups');
	}

	/**
	 * 获得需要显示的权限点
	 *
	 * @return array
	 */
	private function _getShowPoint() {
		return array(
			'allow_visit',
			'user_binding',
			'allow_report',
			'message_allow_send',
			'message_max_send',
			'invite_allow_buy',
			'allow_publish_vedio',
			'allow_publish_music',
			'allow_publish_tag',
			'allow_publish_at',
			'allow_post',
			'allow_reply',
			'allow_read',
			'allow_add_vote',
			'allow_participate_vote',
			'allow_view_vote',
			'thread_award',
			'remote_download',
			'allow_sign',
			'threads_perday',
			'allow_upload',
			'allow_download',
			'uploads_perday',
			'upload_file_types',
			'remind_open',
			'remind_max_num');
	}
}