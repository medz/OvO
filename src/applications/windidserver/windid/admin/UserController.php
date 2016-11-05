<?php
Wind::import('APPS:windid.admin.WindidBaseController');
/**
 * 后台用户管理界面
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: UserController.php 24723 2013-02-17 09:14:43Z jieyin $
 * @package 
 */
class UserController extends WindidBaseController {
	
		
	private $pageNumber = 10;

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		/* @var $groupDs PwUserGroups */
		list($sName, $sUid, $sEmail, $page) = $this->getInput(array('username', 'uid', 'email',  'page'));
		Wind::import('WSRV:user.vo.WindidUserSo');
		$vo = new WindidUserSo();
		$sName && $vo->setUsername($sName);
		$sUid && $vo->setUid($sUid);
		$sEmail && $vo->setEmail($sEmail);
		
		$page = intval($page) == 0 ? 1 : abs(intval($page));
		/* @var $searchDs PwUserSearch */
		$searchDs = Wekit::load('WSRV:user.WindidUser');
		$count = $searchDs->countSearchUser($vo);

		$result = array();
		if (0 < $count) {
			$totalPage = ceil($count/$this->pageNumber);
			$page > $totalPage && $page = $totalPage;
			list($start, $limit) = Pw::page2limit($page, $this->pageNumber);
			$result = $searchDs->searchUser($vo, $limit, $start);
		}
		$data = $vo->getData();
		$this->setOutput($data, 'args');
		$this->setOutput($page, 'page');
		$this->setOutput($this->pageNumber, 'perPage');
		$this->setOutput($count, 'count');
		$this->setOutput($result, 'list');
	}

	/** 
	 * 添加用户
	 * 
	 * @return void
	 */
	public function addAction() {
		if ($this->getInput('type', 'post') === 'do') {
			Wind::import('WSRV:user.dm.WindidUserDm');
			$dm = new WindidUserDm();
			$dm->setUsername($this->getInput('username', 'post'))
				->setPassword($this->getInput('password', 'post'))
			    ->setEmail($this->getInput('email', 'post'))
			    ->setRegdate(Pw::getTime())
				->setRegip($this->getRequest()->getClientIp());
				
			$result = Wekit::load('WSRV:user.WindidUser')->addUser($dm);
			if ($result instanceof PwError) {
				$this->showError($result->getError());
			}

			Wekit::load('WSRV:user.srv.WindidUserService')->defaultAvatar($result);
			$srv = Wekit::load('WSRV:notify.srv.WindidNotifyService');
			$srv->send('addUser', array('uid' => $result));

			$this->showMessage('WINDID:success');
		}
	}

	/** 
	 * 编辑用户信息
	 *
	 * @return void
	 */
	public function editAction() {
		$uid = (int)$this->getInput('uid', 'get');
		$user = Wekit::load('WSRV:user.WindidUser');
		$_info = $user->getUserByUid($uid, WindidUser::FETCH_ALL);
		if (!$_info) $this->showError('WINDID:fail');
		$tYear = Pw::time2str(Pw::getTime(), 'Y');
		$birMin = $tYear-100;
		$birMax = $tYear + 100;
		$this->setOutput($this->_buildArea($_info['location']), 'location');
		$this->setOutput($this->_buildArea($_info['hometown']), 'hometown');
		$this->setOutput($birMin . '-01-01', 'bmin');
		$this->setOutput($birMax . '-12-31', 'bmax');
		$this->setOutput($_info, 'info');
		$this->setOutput($_info['online'] / 3600, 'online');
		$this->setOutput($uid, 'uid');
	}

	/** 
	 * 编辑用户信息操作
	 * 
	 * @return voido
	 */
	public function doEditAction() {
		$uid = (int)$this->getInput('uid', 'post');
		if (!$uid) $this->showError('WINDID:fail');
		Wind::import('WSRV:user.dm.WindidUserDm');
		$dm = new WindidUserDm($uid);
		
		//用户信息
		$dm->setUsername($this->getInput('username', 'post'));
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
		/* @var $srv WindidAreaService */
		$srv = Wekit::load('WSRV:area.srv.WindidAreaService');
		$areas = $srv->fetchAreaInfo(array($hometown, $location));
		$dm->setLocation($location, isset($areas[$location]) ? $areas[$location] : '');
		$dm->setHometown($hometown, isset($areas[$hometown]) ? $areas[$hometown] : '');
		$dm->setHomepage($this->getInput('homepage', 'post'));
		$dm->setProfile($this->getInput('profile', 'post'));
		
		//交易信息
		$dm->setAlipay($this->getInput('alipay', 'post'));
		$dm->setMobile($this->getInput('mobile', 'post'));
		
		
		//联系信息
		$dm->setEmail($this->getInput('email', 'post'));
		$dm->setAliww($this->getInput('aliww', 'post'));
		$dm->setQq($this->getInput('qq', 'post'));
		$dm->setMsn($this->getInput('msn', 'post'));
		
	
		$ds = Wekit::load('WSRV:user.WindidUser');
		$result = $ds->editUser($dm);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$srv = Wekit::load('WSRV:notify.srv.WindidNotifyService');
		$srv->send('editUser', array('uid' => $uid, 'changepwd' => $dm->password ? 1 : 0));

		$this->showMessage('WINDID:success', 'windid/user/edit?uid=' . $uid);
	}

	
	/**
	 * 恢复系统头像
	 */
	public function defaultAvatarAction() {
		$uid = (int)$this->getInput('uid', 'get');
		if (!$uid) $this->showError('WINDID:fail');
		$api = WindidApi::api('avatar');
		if ($api->defaultAvatar($uid) > 0) $this->showMessage('success');
		$this->showError('WINDID:fail');
	}
	
	/** 
	 * 清理用户信息
	 * 
	 * @return void
	 */
	public function deleteAction() {
		$uid = $this->getInput('uid', 'get');
		if (!$uid) $this->showError('WINDID:fail');
		$ds = Wekit::load('WSRV:user.WindidUser');
		$ds->deleteUser($uid);

		$srv = Wekit::load('WSRV:notify.srv.WindidNotifyService');
		$srv->send('deleteUser', array('uid' => $uid));

		$this->showMessage('WINDID:success');
	}

	public function editCreditAction() {
		$uid = $this->getInput('uid', 'get');
		if (!$uid) $this->showError('WINDID:fail');
		//Wind::import('WSRV:user.dm.WindidUserDm');
		//$dm = new WindidUserDm($uid);
		
		$service = $this->_getConfigDs();
		$config = $service->getValues('credit');
		$user = Wekit::load('WSRV:user.WindidUser');
		$info = $user->getUserByUid($uid, WindidUser::FETCH_DATA);
		if (!$info) $this->showError('WINDID:fail');
		$userCreditDb = array();
		foreach ($config['credits'] AS  $k => $value) {
			if (isset($info['credit' . $k])) {
				$userCreditDb[$k] = array('name' => $value['name'], 'num' => $info['credit' . $k]);
			}
		}
		$this->setOutput($uid, 'uid');
		$this->setOutput($userCreditDb, 'credits');
	}
	
	/** 
	 * 设置用户积分
	 * 
	 * @return void
	 */
	public function doEditCreditAction() {
		$uid = $this->getInput('uid', 'post');
		if (!$uid) $this->showError('WINDID:fail');
		$credits = $this->getInput("credit");
		Wind::import('WSRV:user.dm.WindidCreditDm');
		$dm = new WindidCreditDm($uid);
		foreach ($credits as $id => $value) {
			$dm->setCredit($id, $value);
		}
		
		$ds = Wekit::load('WSRV:user.WindidUser');
		$result = $ds->updateCredit($dm);
		if ($result instanceof WindidError) {
			$this->showError($result->getCode());
		}
		$srv = Wekit::load('WSRV:notify.srv.WindidNotifyService');
		$srv->send('editCredit', array('uid' => $uid));

		$this->showMessage('WINDID:success', 'windid/user/editCredit?uid=' . $uid);
	}

	/**
	 * @return PwCreditSetService
	 */
	private function _getPwCreditService() {
		return Wekit::load("credit.srv.PwCreditSetService");
	}
	
	private function _getConfigDs() {
		return Wekit::load('WSRV:config.WindidConfig');
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
		/* @var $areaSrv WindidAreaService */
		$areaSrv = Wekit::load('WSRV:area.srv.WindidAreaService');
		$rout = $areaSrv->getAreaRout($areaid);
		return WindUtility::mergeArray($default, $rout);
	}
}