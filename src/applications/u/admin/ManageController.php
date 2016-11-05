<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:user.vo.PwUserSo');
Wind::import('SRV:user.srv.PwClearUserService');

/**
 * 后台用户管理界面
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: ManageController.php 24850 2013-02-25 02:20:12Z jieyin $
 * @package 
 */
class ManageController extends AdminBaseController {
	
	private $upgradeGroups = array('name' => '普通组', 'gid' => '0');
		
	private $pageNumber = 10;

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');
		$groups = $groupDs->getNonUpgradeGroups();
		$groups[0] = $this->upgradeGroups;
		ksort($groups);
		list($sName, $sUid, $sEmail, $sGroup, $page) = $this->getInput(array('username', 'uid', 'email', 'gid', 'page'));
		$vo = new PwUserSo();
		$sName && $vo->setUsername($sName);
		$sUid && $vo->setUid($sUid);
		$sEmail && $vo->setEmail($sEmail);
		(!$sGroup || in_array(-1, $sGroup)) || $vo->setGid($sGroup);
		$page = intval($page) == 0 ? 1 : abs(intval($page));
		/* @var $searchDs PwUserSearch */
		$searchDs = Wekit::load('SRV:user.PwUserSearch');
		$count = $searchDs->countSearchUser($vo);

		$result = array();
		if (0 < $count) {
			$totalPage = ceil($count/$this->pageNumber);
			$page > $totalPage && $page = $totalPage;
			/* @var $searchDs PwUserSearch */
			$searchDs = Wekit::load('user.PwUserSearch');
			list($start, $limit) = Pw::page2limit($page, $this->pageNumber);
			$result = $searchDs->searchUser($vo, $limit, $start);
			if ($result) {
				/* @var $userDs PwUser */
				$userDs = Wekit::load('user.PwUser');
				$list = $userDs->fetchUserByUid(array_keys($result), PwUser::FETCH_DATA);
				$result = WindUtility::mergeArray($result, $list);
			}
		}
		$data = $vo->getData();
		(!$sGroup || in_array(-1, $sGroup)) && $data['gid'] = array(-1);
		$this->setOutput($data, 'args');
		$this->setOutput($page, 'page');
		$this->setOutput($this->pageNumber, 'perPage');
		$this->setOutput($count, 'count');
		$this->setOutput($result, 'list');
		
		$this->setOutput($groups, 'groups');
	}

	/** 
	 * 添加用户
	 * 
	 * @return void
	 */
	public function addAction() {
		if ($this->getInput('type', 'post') === 'do') {
			Wind::import('SRC:service.user.dm.PwUserInfoDm');
			$dm = new PwUserInfoDm();
			$dm->setUsername($this->getInput('username', 'post'))
				->setPassword($this->getInput('password', 'post'))
			    ->setEmail($this->getInput('email', 'post'))
			    ->setRegdate(Pw::getTime())
				->setRegip($this->getRequest()->getClientIp());
			$groupid = $this->getInput('groupid', 'post');
			$dm->setGroupid($groupid);
			if ($groupid != 0) {
				// 默认组不保存到groups
				/* @var $groupDs PwUserGroups */
				$groupDs = Wekit::load('usergroup.PwUserGroups');
				$groups = $groupDs->getGroupsByType('default');
				if (!in_array($groupid, array_keys($groups))) {
					$dm->setGroups(array($groupid => 0));
				}
			}
			/* @var $groupService PwUserGroupsService */
			$groupService = Wekit::load('usergroup.srv.PwUserGroupsService');
			$memberid = $groupService->calculateLevel(0);
			$dm->setMemberid($memberid);
				
			$result = Wekit::load('user.PwUser')->addUser($dm);
			if ($result instanceof PwError) {
				$this->showError($result->getError());
			}
			//添加站点统计信息
			Wind::import('SRV:site.dm.PwBbsinfoDm');
			$bbsDm = new PwBbsinfoDm();
			$bbsDm->setNewmember($dm->getField('username'))->addTotalmember(1);
			Wekit::load('site.PwBbsinfo')->updateInfo($bbsDm);
			//Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar($result);
			$this->showMessage('USER:add.success');
		}
		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');
		$groups = $groupDs->getClassifiedGroups();
		unset($groups['system'][5]);//排除“版主”
		$result = array_merge($groups['special'], $groups['system']);
		$this->setOutput($result, 'groups');
	}

	/** 
	 * 编辑用户信息
	 *
	 * @return void
	 */
	public function editAction() {
		$info = $this->checkUser();
		/* @var $pwUser PwUser */
		$pwUser = Wekit::load('user.PwUser');
		$_info = $pwUser->getUserByUid($info['uid'], PwUser::FETCH_ALL);
		$_winfo = WindidApi::api('user')->getUser($info['uid']);
		$_info['regip'] = $_winfo['regip'];
		
		$tYear = Pw::time2str(Pw::getTime(), 'Y');
		$birMin = $tYear-100;
		$birMax = $tYear + 100;
		$this->setOutput($this->_buildArea($_info['location']), 'location');
		$this->setOutput($this->_buildArea($_info['hometown']), 'hometown');
		$this->setOutput($birMin . '-01-01', 'bmin');
		$this->setOutput($birMax . '-12-31', 'bmax');
		$this->setOutput($_info, 'info');
		$this->setOutput(round($_info['onlinetime'] / 3600), 'online');
		
		//可能的扩展点
		$work = Wekit::load('SRV:work.PwWork')->getByUid($info['uid']);
		$education = Wekit::load('SRV:education.srv.PwEducationService')->getEducationByUid($info['uid'], 100);
		$this->setOutput($work ,'workList');
		$this->setOutput($education, 'educationList');
	}

	/** 
	 * 编辑用户信息操作
	 * 
	 * @return voido
	 */
	public function doEditAction() {
		$info = $this->checkUser();
		
		Wind::import('SRC:service.user.dm.PwUserInfoDm');
		$dm = new PwUserInfoDm($info['uid']);
		
		//用户信息
		//$dm->setUsername($this->getInput('username', 'post'));
		list($password, $repassword) = $this->getInput(array('password', 'repassword'), 'post');
		if ($password) {
			if ($password != $repassword) $this->showError('USER:user.error.-20');
			$dm->setPassword($password);
		}
		$dm->setEmail($this->getInput('email', 'post'));
		
		list($question, $answer) = $this->getInput(array('question', 'answer'), 'post');
		switch ($question) {
			case '-2':
			 	$dm->setQuestion('', '');
			 	break;
			case '-1':
			default :
				break;
		}

		$dm->setRegdate(Pw::str2time($this->getInput('regdate', 'post')));
		$dm->setRegip($this->getInput('regip', 'post'));
		$dm->setOnline(intval($this->getInput('online', 'post')) * 3600);
		
		//基本资料
		$dm->setRealname($this->getInput('realname', 'post'));
		$dm->setGender($this->getInput('gender', 'post'));
		$birthday = $this->getInput('birthday', 'post');
		if ($birthday) {
			$bir = explode('-', $birthday);
			isset($bir[0]) && $dm->setByear($bir[0]);
			isset($bir[1]) && $dm->setBmonth($bir[1]);
			isset($bir[2]) && $dm->setBday($bir[2]);
		} else {
			$dm->setBday('')->setByear('')->setBmonth('');
		}
		list($hometown, $location) = $this->getInput(array('hometown', 'location'), 'post');

		$srv = WindidApi::api('area');
		$areas = $srv->fetchAreaInfo(array($hometown, $location));
		$dm->setLocation($location, isset($areas[$location]) ? $areas[$location] : '');
		$dm->setHometown($hometown, isset($areas[$hometown]) ? $areas[$hometown] : '');
		$dm->setHomepage($this->getInput('homepage', 'post'));
		$dm->setProfile($this->getInput('profile', 'post'));
		
		//交易信息
		$dm->setAlipay($this->getInput('alipay', 'post'));
		$dm->setMobile($this->getInput('mobile', 'post'));
		$dm->setTelphone($this->getInput('telphone', 'post'));
		$dm->setAddress($this->getInput('address', 'post'));
		$dm->setZipcode($this->getInput('zipcode', 'post'));
		
		//联系信息
		$dm->setEmail($this->getInput('email', 'post'));
		$dm->setAliww($this->getInput('aliww', 'post'));
		$dm->setQq($this->getInput('qq', 'post'));
		$dm->setMsn($this->getInput('msn', 'post'));
		
		/* @var $pwUser PwUser */
		$pwUser = Wekit::load('user.PwUser');
		$result = $pwUser->editUser($dm);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$isFounder = $this->isFounder($info['username']);
		$this->showMessage($isFounder ? 'USER:founder.update.success' : 'USER:update.success', 'u/manage/edit?uid=' . $info['uid']);
	}

	/** 
	 * 编辑用户积分
	 * 
	 * @return void
	 */
	public function editCreditAction() {
		$info = $this->checkUser();
		/* @var $pwUser PwUser */
		$pwUser = Wekit::load('user.PwUser');
		$userCredits = $pwUser->getUserByUid($info['uid'], PwUser::FETCH_DATA);
		$userCreditDb = array();
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		/* @var $pwCreditBo PwCreditBo */
		$pwCreditBo = PwCreditBo::getInstance();
		
		foreach ($pwCreditBo->cType as $k => $value) {
			if (isset($userCredits['credit' . $k])) {
				$userCreditDb[$k] = array('name' => $value, 'num' => $userCredits['credit' . $k]);
			}
		}
		$this->setOutput($userCreditDb, 'credits');
	}

	/** 
	 * 设置用户积分
	 * 
	 * @return void
	 */
	public function doEditCreditAction() {
		$info = $this->checkUser();
		$credits = $this->getInput("credit");
		/* @var $pwUser PwUser */
		$pwUser = Wekit::load('user.PwUser');
		$userCredits = $pwUser->getUserByUid($info['uid'], PwUser::FETCH_DATA);
		$changes = array();
		foreach ($credits as $id => $value) {
			$org = isset($userCredits['credit' . $id]) ? $userCredits['credit' . $id] : 0;
			$changes[$id] = $value - $org;
		}
		
		Wind::import('SRV:credit.bo.PwCreditBo');
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->addLog('admin_set', $changes, new PwUserBo($this->loginUser->uid));
		$creditBo->execute(array($info['uid'] => $credits), false);
		$this->showMessage('USER:update.success', 'u/manage/editCredit?uid=' . $info['uid']);
	}

	/** 
	 * 设置用户组
	 * 
	 * @return void
	 */
	public function editGroupAction() {
		$info = $this->checkUser();

		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');

		/* @var $groups 将包含有特殊组和管理组 */
		$systemGroups = $groupDs->getClassifiedGroups();
		$groups = array();
		foreach (array('system','special','default') as $k) {
			foreach ($systemGroups[$k] as $gid => $_item) {
				if (in_array($gid, array(1, 2))) continue;
				$groups[$gid] = $_item;
			}
		}

		/* @var $belongDs PwUserBelong */
		$belongDs = Wekit::load('user.PwUserBelong');
		$userGroups = $belongDs->getUserBelongs($info['uid']);
		
		$this->setOutput(array_keys($systemGroups['default']), 'defaultGroups');
		$this->setOutput($userGroups, 'userGroups');
		$this->setOutput($groups, 'allGroups');
		$this->setOutput($info, 'info');
	}

	/** 
	 * 操作用户组设置
	 * 
	 * @return void
	 */
	public function doEditGroupAction() {

		$info = $this->checkUser();
		list($groupid, $groups, $endtime) = $this->getInput(array('groupid', 'groups', 'endtime'), 'post');
		/* @var $groupDs PwUserGroups */
		$groupDs = Wekit::load('usergroup.PwUserGroups');
		$banGids = array_keys($groupDs->getGroupsByType('default'));//默认用户组
		$clearGids = array();
		
		//如果用户原先的用户组是在默认组中，则该用户组不允许被修改
		if (in_array($info['groupid'], $banGids) && $info['groupid'] != $groupid) {
			switch($info['groupid']) {
				case 6:
					$this->showError('USER:user.belong.delban.error');
					break;
				case 7:
					$this->showError('USER:user.belong.delactive.error');
					break;
				default :
					$this->showError('USER:user.belong.default.error');
					break;
			}
		}
		//如果用户原先的用户组是不在默认组中，新设置的用户组在默认组中，则抛错
		if (!in_array($info['groupid'], $banGids) && in_array($groupid, $banGids) && $info['groupid'] != $groupid) {
			switch($groupid) {
				case 6:
					$this->showError('USER:user.belong.ban.error');
					break;
				case 7:
					$this->showError('USER:user.belong.active.error');
					break;
				default :
					$this->showError('USER:user.belong.default.error');
					break;
			}
		}
		
		if (($if = in_array($groupid, $banGids)) || ($r = array_intersect($banGids, $groups))) {
			$this->showError('USER:user.belong.default.error');
// 			(!$if && $r) && $groupid = array_shift($r);
		} else {
			foreach ($groups as $value) {
				$clearGids[$value] = (isset($endtime[$value]) && $endtime[$value]) ? Pw::str2time($endtime[$value]) : 0;
			}
			if ($groupid == 0) {
				/* @var $userService PwUserService */
				$userService = Wekit::load('user.srv.PwUserService');
				list($groupid, $clearGids) = $userService->caculateUserGroupid($groupid, $clearGids);
			} elseif (!isset($clearGids[$groupid])) {
				$clearGids[$groupid] = 0;
			}
		}

		$oldGid = explode(',', $info['groups']);
		$info['groupid'] && array_push($oldGid, $info['groupid']);
		//总版主处理
		if (in_array(5, $oldGid) && !isset($clearGids[5])) {
			$this->showError('USER:user.forumadmin.delete.error');
		}
		if (!in_array(5, $oldGid) && isset($clearGids[5])) {
			$this->showError('USER:user.forumadmin.add.error');
		}

		Wind::import('SRV:user.dm.PwUserInfoDm');
		$dm = new PwUserInfoDm($info['uid']);
		$dm->setGroupid($groupid)
			->setGroups($clearGids);

		/* @var $userDs PwUser */
		$userDs = Wekit::load('user.PwUser');
		$result = $userDs->editUser($dm, PwUser::FETCH_MAIN);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		
		/* if (in_array($groupid, $banGids)) {
			Wekit::load('SRV:forum.srv.PwForumMiscService')->updateDataByUser($info['username']);
		} */
		
		$this->showMessage('USER:update.success', 'u/manage/editGroup?uid=' . $info['uid']);
	}

	/**
	 * 恢复系统头像
	 */
	public function defaultAvatarAction() {
		$info = $this->checkUser();
		$p = Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar($info['uid']);
		if ($p === false) {
			$this->showError('operate.fail');
		}
		$this->showMessage('success');
	}
	
	/** 
	 * 清理用户信息
	 * 
	 * @return void
	 */
	public function clearAction() {
		$info = $this->checkUser();
		/* @var $userSer PwClearUserService */
		$userSer = Wekit::load('user.srv.PwClearUserService');
		$this->setOutput($userSer->getClearTypes(), 'types');
	}
	
	/** 
	 * 清理用户操作
	 * 
	 * @return void
	 */
	public function doClearAction() {
		$info = $this->checkUser();
		/* @var $userSer PwClearUserService */
		$userSer = new PwClearUserService($info['uid'], new PwUserBo($this->loginUser->uid));
		if (($result = $userSer->run($this->getInput('clear', 'post'))) instanceof PwError) {
			$this->showError($result->getError(), 'admin/u/manage/run');
		}
		$this->showMessage('USER:clear.success', 'admin/u/manage/run');
	}

	/** 
	 * 检查用户信息同时返回用户对象
	 *
	 * @return PwUserBo
	 */
	private function checkUser() {
		$uid = $this->getInput('uid');
		if ($uid <= 0) $this->forwardAction('admin/u/manage/run');
		/* @var $pwUser PwUser */
		$pwUser = Wekit::load('user.PwUser');
		$info = $pwUser->getUserByUid($uid);
		if (!$info) $this->showError('USER:illega.id', 'admin/u/manage/run');
		$this->setOutput($uid, 'uid');
		$this->setOutput($info['username'], 'username');
		return $info;
	}

	/**
	 * @return PwCreditSetService
	 */
	private function _getPwCreditService() {
		return Wekit::load("credit.srv.PwCreditSetService");
	}

	/**
	 * 设置地区显示
	 * 
	 * @return array
	 */
	private function _buildArea($areaid) {
		$default = array(array('areaid' => '', 'name' => ''), array('areaid' => '', 'name' => ''), array('areaid' => '', 'name' => ''));
		if (!$areaid) {
			return $default;
		}
		$rout = WindidApi::api('area')->getAreaRout($areaid);
		return WindUtility::mergeArray($default, $rout);
	}
}