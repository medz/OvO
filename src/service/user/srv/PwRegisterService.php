<?php
Wind::import('SRV:service.user.validator.PwUserValidator');
Wind::import('LIB:utility.PwMail');
Wind::import('SRV:credit.bo.PwCreditBo');
Wind::import('SRV:user.PwUser');

/**
 * 用户注册
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwRegisterService.php 25182 2013-03-06 07:54:07Z long.shi $
 * @package src.service.user.srv
 */
class PwRegisterService extends PwBaseHookService {
	public $config = null;
	private $activeCodeValidTime = 3;//激活码有效期，单位小时
	/* @var $userDm PwUserInfoDm */
	private $userDm = null;
	/* @var $isOpenInvite int 是否开启邀请注册，开启时该值为1 */
	public $isOpenInvite = 0;
	/* @var $isOpenMobileCheck int 是否开启手机验证，开启时该值为1 */
	public $isOpenMobileCheck = 0;

	public function __construct() {
		parent::__construct();
		$this->config = Wekit::C('register');
		$this->isOpenInvite = (2 == $this->config['type'] ? 1 : 0);
		$this->isOpenMobileCheck = (1 == $this->config['active.phone'] ? 1 : 0);
	}

	/**
	 * 检查是否设置同一IP设置一段时间内不能注册
	 *
	 * @param string $ip 待检查的IP
	 * @return true|PwError
	 */
	public function checkIp($ip) {
		if (!($ipSpace = abs($this->config['security.ip']))) return true;
		$space = $ipSpace * 3600;
		/* @var $registerDs PwUserRegisterIp */
		$registerDs = Wekit::load('user.PwUserRegisterIp');
		$data = $registerDs->getRecodeByIp($ip);
		if (!$data || Pw::getTime() - $data['last_regdate'] > $space) return true;
		return new PwError('USER:register.error.security.ip', array('{ipSpace}' => $ipSpace));
	}

	/** 
	 * 设置用户信息
	 *
	 * @param PwUserInfoDm $userForm
	 */
	public function setUserDm(PwUserInfoDm $userDm) {
		$this->userDm = $this->filterUserDm($userDm);
	}

	/** 
	 * 返回用户信息的DM
	 *
	 * @return PwUserInfoDm
	 */
	public function getUserDm() {
		return $this->userDm;
	}

	/** 
	 * 用户注册信息
	 * 
	 * @return boolean|int
	 */
	public function register() {
		if (!$this->userDm) return new PwError('USER：illegal.request');
		if (($result = $this->checkIp($this->userDm->getField('regip'))) instanceof PwError) {
			return $result;
		}
		//[c_register]:调用插件中用户注册操作的前置方法beforeRegister
		if (($result = $this->runWithVerified('beforeRegister', $this->userDm)) instanceof PwError) {
			return $result;
		}
        if (($uid = $this->_getUserDs()->addUser($this->userDm)) instanceof PwError) {
			return $uid;
		}
        //记录一下ip次数
        $this->_recordIpLimit($uid);
        //
		$this->userDm->setUid($uid);
		return $this->afterRegister($this->userDm);
	}

	/**
	 * 同步用户数据
	 * 
	 * 如果本地有用户数据
	 * 如果本地没有用户数据，则将用户数据从windid同步过来
	 *
	 * @param int $uid
	 * @return array
	 */
	public function sysUser($uid) {
		$info = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_MAIN);
		if (!$info) {
			//从windid这边将数据同步到论坛
			if (!$this->_getUserDs()->activeUser($uid)) return false;
			//更新用户信息
			$pwUserInfoDm = new PwUserInfoDm($uid);
			$_userinfo = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_MAIN | PwUser::FETCH_DATA);
			$this->_getUserDs()->editUser($this->filterUserDm($pwUserInfoDm, $_userinfo));
			Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar($info['uid']);

			$pwUserInfoDm->setUsername($_userinfo['username']);
			$pwUserInfoDm->setEmail($_userinfo['email']);
			$pwUserInfoDm->setRegip(Wind::getComponent('request')->getClientIp());
			$info = $this->afterRegister($pwUserInfoDm);
			$this->sendEmailActive($_userinfo['username'], $_userinfo['email']);
		}
		return $info;
	}

	/** 
	 * 需要发送新用户激活邮件
	 * 
	 * @param string $username 用户名
	 * @param string $email 用户邮件
	 * @param string $statu 激活标志
	 * @param int $uid 用户ID
	 * @return boolean
	 */
	public function sendEmailActive($username, $email, $statu = '', $uid = 0) {
		if (!$this->config['active.mail']) return true;
		if ($uid == 0 || !$statu) {
			$info = $this->_getUserDs()->getUserByName($username, PwUser::FETCH_MAIN);
			if ($info['email'] != $email) return new PwError('USER:illegal.request');
			$uid = $info['uid'];
			$statu = self::createRegistIdentify($uid, $info['password']);
		}
		if (!Wind::getComponent('router')->getRoute('pw')) {
			Wind::getComponent('router')->addRoute('pw', WindFactory::createInstance(Wind::import('LIB:route.PwRoute'), array('bbs')));
		}

		$code = substr(md5(Pw::getTime()), mt_rand(1, 8), 8);
		$url = WindUrlHelper::createUrl('u/register/activeEmail', array('code' => $code, '_statu' => $statu), '', 'pw');
		list($title, $content) = $this->_buildTitleAndContent('active.mail.title', 'active.mail.content', $username, $url);
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$activeCodeDs->addActiveCode($uid, $email, $code, Pw::getTime());
		$mail = new PwMail();
		$mail->sendMail($email, $title, $content);
		return true;
	}
	
	/** 
	 * 检查是否已经发送了激活邮箱
	 *
	 * @param int $uid 用户ID
	 * @param string $email 用户邮箱
	 * @return boolean
	 */
	public function checkIfActiveEmailSend($uid, $email) {
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$info = $activeCodeDs->getInfoByUid($uid);
		if (!$info || $info['email'] != $email || $info['active_time'] > 0) return false;
		$validTime = $this->activeCodeValidTime * 3600;
		//过期了
		if (($info['send_time'] + $validTime) < Pw::getTime()) return false;
		return true;
	}
	
	/** 
	 * 激活email
	 *
	 * @param int $uid 用户ID
	 * @param string $email 用户Email
	 * @param string $code 激活码
	 * @return boolean
	 */
	public function activeEmail($uid, $email, $code) {
		/* @var $activeCodeDs PwUserActiveCode */
		$activeCodeDs = Wekit::load('user.PwUserActiveCode');
		$info = $activeCodeDs->getInfoByUid($uid);
		if (!$info || $info['email'] != $email || $info['code'] != $code) return new PwError("USER:illegal.request");
		if ($info['active_time'] > 0 ) return new PwError('USER:active.email.dumplicate');
		$validTime = $this->activeCodeValidTime * 3600;
		if (($info['send_time'] + $validTime) < Pw::getTime()) return new PwError('USER:active.email.overtime');
		$activeCodeDs->activeCode($uid, Pw::getTime());
		
		$info = $this->_getUserDs()->getUserByUid($uid, PwUser::FETCH_MAIN);
		if (Pw::getstatus($info['status'], PwUser::STATUS_UNACTIVE)) {
			$userDm = new PwUserInfoDm($info['uid']);
			$userDm->setUnactive(false);
			(!Pw::getstatus($info['status'], PwUser::STATUS_UNCHECK)) && $userDm->setGroupid(0);
			$this->_getUserDs()->editUser($userDm, PwUser::FETCH_MAIN);
		}
		/* @var $registerCheckDs PwUserRegisterCheck */
		$registerCheckDs = Wekit::load('user.PwUserRegisterCheck');
		$registerCheckDs->activeUser($uid);
		return true;
	}
	
	/** 
	 * 发送欢迎信息
	 *
	 * @param int $uid 用户ID
	 * @param string $username 用户名
	 * @param string $email 邮箱
	 * @return boolean
	 */
	public function sendWelcomeMsg($uid, $username, $email) {
		list($title, $content) = $this->_buildTitleAndContent('welcome.title', 'welcome.content', $username);
		if (in_array(1, $this->config['welcome.type'])) {
			/* @var $notice PwNoticeService */
			$notice = Wekit::load('message.srv.PwNoticeService');
			$notice->sendDefaultNotice($uid, $content, $title);
		}
		
		/*如果含有激活邮件则发送到*/
		if (!in_array(2, $this->config['welcome.type'])) return true;
		//如果是邮件激活开启，则不需要发送欢迎邮件
		if ($this->config['active.mail'] == 1) return true;
		$mail = new PwMail();
		$mail->sendMail($email, $title, $content);
		return true;
	}

	/** 
	 * 构造用户标志
	 *
	 * @param int $uid  用户ID
	 * @param string $pwd 用户密码
	 * @return string
	 */
	public static function createRegistIdentify($uid, $pwd) {
		$code = Pw::encrypt($uid . "\t" . Pw::getPwdCode($pwd));
		return rawurlencode($code);
	}
	
	/** 
	 * 检查用户标志
	 *
	 * @param string $identify
	 * @return array array($uid, $password);
	 */
	public static function parserRegistIdentify($identify) {
		return explode("\t", Pw::decrypt(rawurldecode($identify)));
	}

	/**
	 *  完成注册的后期执行
	 *
	 * @param PwUserInfoDm $userDm
	 * @return array
	 */
	protected function afterRegister(PwUserInfoDm $userDm) {
		//Wekit::load('user.srv.PwUserService')->restoreDefualtAvatar($uid); windid处理
		//获得注册积分
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditBo->operate('register', new PwUserBo($userDm->uid));
		
		$this->updateRegisterIp($userDm->getField('regip'), Pw::getTime());
		$this->updateRegisterCheck($userDm->uid);
		$this->sendWelcomeMsg($userDm->uid, $userDm->getField('username'), $userDm->getField('email'));
		
		//[c_register]:调用插件中用户注册操作的后置方法afterRegister
		if (($result = $this->runWithVerified('afterRegister', $userDm)) instanceof PwError) return $result;
		return $this->_getUserDs()->getUserByUid($userDm->uid, PwUser::FETCH_MAIN);
	}
	
	/**
	 * 过滤用户DM同时设置用户的相关信息
	 * 
	 * @param PwUserInfoDm $userDm
	 * @param array $hasCredit
	 * @return PwUserInfoDm
	 */
	protected function filterUserDm(PwUserInfoDm $userDm, $hasCredit = array()) {
		//如果开启邮箱激活，则设置该状态为0，否则设置该状态为1
		$_uncheckGid = false;
		if ($this->config['active.mail']) {
			$userDm->setUnactive(true);
			$_uncheckGid = true;
		}
		//如果开启审核，则设置该状态为0，否则设置该状态为1
		if ($this->config['active.check']) {
			$userDm->setUncheck(true);
			$_uncheckGid = true;
		}
		//【用户注册】未验证用户组
		if ($_uncheckGid) {
			$userDm->setGroupid(7);
			$userDm->setGroups(array());
		}
		$_credit = $this->_getRegisterAddCredit($hasCredit);
		//【用户注册】计算memberid
		/* @var $groupService PwUserGroupsService */
		$groupService = Wekit::load('usergroup.srv.PwUserGroupsService');
		$credit = $groupService->calculateCredit(Wekit::C('site', 'upgradestrategy'), $_credit);
		$memberid = $groupService->calculateLevel($credit);
		$userDm->setMemberid($memberid);
		return $userDm;
	}
	
	/**
	 * 获取注册可以添加的积分
	 *
	 * @param array $_credit  已有的积分
	 * @return array
	 */
	private function _getRegisterAddCredit($_credit = array()) {
		//【用户注册】注册成功初始积分---积分策略中获取
		/* @var $creditBo PwCreditBo */
		$creditBo = PwCreditBo::getInstance();
		$creditStrategy = $creditBo->getStrategy('register');
		!$creditStrategy['credit'] && $creditStrategy['credit'] = array();
		foreach ($creditStrategy['credit'] as $id => $_v) {
			$_id = 'credit' . $id;
			if (isset($_credit[$_id])) {
				$_credit[$_id] += $_v;
			} else {
				$_credit[$_id] = $_v;
			}
		}
		return $_credit;
	}
	
	/** 
	 * 获得信息的标题和内容
	 *
	 * @param string $titleKey   标题key
	 * @param string $contentKey 内容key
	 * @param string $username 用户名
	 * @param string $url 链接地址
	 * @return array
	 */
	private function _buildTitleAndContent($titleKey, $contentKey, $username, $url = '') {
		$search = array('{username}', '{sitename}');
		$replace = array($username, Wekit::C('site', 'info.name'));
		$title = str_replace($search, $replace, $this->config[$titleKey]);
		$search[] = '{time}';
		$search[] = '{url}';
		$replace[] = Pw::time2str(Pw::getTime(), 'Y-m-d H:i:s');
		$replace[] = $url ? sprintf('<a href="%s">%s</a>', $url, $url) : '';
		$content = str_replace($search, $replace, $this->config[$contentKey]);
		return array($title, $content);
	}

	/** 
	 * 更新注册IP 
	 *
	 * @param string $ip 注册的IP
	 * @param int $time 注册时间
	 * @return boolean
	 */
	private function updateRegisterIp($ip, $time) {
		if (!$this->config['security.ip']) return true;
		/* @var $registerDa PwUserRegisterIp */
		$registerDs = Wekit::load('user.PwUserRegisterIp');
		return $registerDs->updateRecodeByIp($ip, $time);
	}
	
	/**
	 * 更新用户注册相关的审核/激活表
	 *
	 * @param int $uid
	 * @return boolean
	 */
	private function updateRegisterCheck($uid) {
		//添加到注册审核表中
		$_ifactive = $this->config['active.mail'] ? 0 : 1;
		$_ifcheck = $this->config['active.check'] ? 0 : 1;
		/* @var $registerCheckDs PwUserRegisterCheck */
		$registerCheckDs = Wekit::load('user.PwUserRegisterCheck');
		$registerCheckDs->addInfo($uid, $_ifcheck, $_ifactive);
		return true;
	}

	/** 
	 * 获得用户DS
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseHookService::_getInterfaceName()
	 */
	protected function _getInterfaceName() {
		return 'PwRegisterDoBase';
    }

    private function _recordIpLimit(){
        Wind::import('SRV:user.srv.PwTryPwdBp');
        $pwdBp = new PwTryPwdBp();     
        $pwdBp->allowTryAgain($uid, Wind::getComponent('request')->getClientIp(), true );
    }
}
