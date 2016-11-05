<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:user.validator.PwUserValidator');
Wind::import('SRV:user.PwUser');

/**
 * 用户数据dm操作
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserInfoDm.php 24943 2013-02-27 03:52:21Z jieyin $
 * @package src.service.user.dm
 */
class PwUserInfoDm extends PwBaseDm {

	public $uid;
	/*@var $dm WindidUserDm */
	public $dm = null;
	protected $_belong = null;
	protected $_password;

	/** 
	 * 构造函数
	 *
	 * @param int $uid
	 */
	public function __construct($uid = 0) {
		$this->uid = $uid;
	}
	
	/**
	 * 设置用户ID
	 *
	 * @param int $uid
	 * @return PwUserInfoDm
	 */
	public function setUid($uid) {
		$this->uid = $uid;
		$this->_data['uid'] = $uid;
		return $this;
	}
	
	/** 
	 * 获取用户WindidDm
	 *
	 * @return WindidUserDm
	 */
	public function getDm() {
		if (!is_object($this->dm)) {
			$dm = WindidApi::getDm('user');
			$this->dm = new $dm($this->uid);
		}
		return $this->dm;
	}

	/**
	 * 设置用户名字
	 * 
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->getDm()->setUsername($username);
		$this->_data['username'] = $username;
		return $this;
	}
	
	/**
	 * 设置老的密码，进行验证
	 *
	 * @param string $oldPwd
	 * @return PwUserInfoDm
	 */
	public function setOldPwd($oldPwd) {
		$this->getDm()->setOldpwd($oldPwd);
		return $this;
	}

	/**
	 * 设置用户密码
	 * 
	 * @param string $password 新密码
	 * @return PwUserInfoDm
	 */
	public function setPassword($password) {
		$this->getDm()->setPassword($password);
		$this->_password = $password;
		$this->_data['password'] = md5(WindUtility::generateRandStr(16));
		return $this;
	}

	/**
	 * 设置用户email
	 * 
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->getDm()->setEmail($email);
		$this->_data['email'] = $email;
		return $this;
	}

	/**
	 * 设置安全问题
	 * 
	 * @param string $question
	 * @param string $answer
	 * @return PwUserInfo
	 */
	public function setQuestion($question, $answer) {
		$this->getDm()->setQuestion($question);
		$this->getDm()->setAnswer($answer);
		return $this;
	}

	/**
	 * 更新状态
	 *
	 * @param int $status
	 * @return PwUserInfoDm
	 */
	public function updateStatus($status) {
		$this->_increaseData['status'] = intval($status);
		return $this;
	}
	
	/**
	 * 设置用户当前用户组
	 *
	 * @param int $gid
	 * @return PwUserInfoDm
	 */
	public function setGroupid($gid) {
		$this->_data['groupid'] = $gid;
		$this->_belong = array();
		$gid > 0 && $this->_belong[$gid] = 0;
		return $this;
	}
	
	/** 
	 * 设置用户拥有的组
	 * 注:使用该方法之前，一定要先使用 setGroupid()
	 *
	 * @param array $groups 拥有的组
	 * @example:
	 * @$groups = array(
	 *		'3' => 0,
	 *		'4' => 1234567890,
	 *		...
	 *	)
	 *  注:key=用户组id  value=过期时间
	 */
	public function setGroups($groups) {
		if (!isset($this->_data['groupid'])) return $this;
		$time = Pw::getTime();
		foreach ($groups as $gid => $endtime) {
			if ($gid && ($endtime == 0 || $endtime > $time)) {
				$this->_belong[$gid] = $endtime;
			}
		}
		if (!$this->_data['groupid'] && $this->_belong) {
			$this->_data['groupid'] = key($this->_belong);
		}
		$this->_data['groups'] = array_diff(array_keys($this->_belong), array($this->_data['groupid']));
		return $this;
	}

	/**
	 * 设置注册IP
	 * 
	 * @param string $regip
	 */
	public function setRegip($regip) {
		$this->getDm()->setRegip($regip);
		return $this;
	}
	
	/**
	 * 设置注册时间戳
	 * 
	 * @param string $regdate
	 */
	public function setRegdate($regdate) {
		$this->getDm()->setRegdate($regdate);
		$this->_data['regdate'] = max(0, intval($regdate));
		return $this;
	}

	/**
	 * 设置性别
	 * 
	 * @param int $gender
	 */
	public function setGender($gender) {
		$this->getDm()->setGender($gender);
		$this->_data['gender'] = intval($gender);
		return $this;
	}

	/**
	 * 设置生日-年
	 * 
	 * @param int $year
	 */
	public function setByear($year) {
		$this->getDm()->setByear($year);
		$this->_data['byear'] = intval($year);
		return $this;
	}
	
	/**
	 * 设置生日-月
	 * 
	 * @param string $month
	 */
	public function setBmonth($month) {
		$this->getDm()->setBmonth($month);
		$this->_data['bmonth'] = $month;
		return $this;
	}
	
	/**
	 * 设置生日-日
	 * 
	 * @param string $bday
	 */
	public function setBday($bday) {
		$this->getDm()->setBday($bday);
		$this->_data['bday'] = $bday;
		return $this;
	}

	/**
	 * 设置家庭地址代码
	 * 
	 * @param int $hometown
	 * @param string $hometown_text 家庭地址的文本信息
	 */
	public function setHometown($hometown, $hometown_text) {
		$this->getDm()->setHometown($hometown);
		$this->_data['hometown'] = intval($hometown);
		$this->_data['hometown_text'] = $hometown_text;
		return $this;
	}

	/**
	 * 设置居住地代码
	 * 
	 * @param int $location
	 * @param string $location_text
	 */
	public function setLocation($location, $location_text) {
		$this->getDm()->setLocation($location);
		$this->_data['location'] = intval($location);
		$this->_data['location_text'] = $location_text;
		return $this;
	}

	/**
	 * 设置主页
	 * 
	 * @param string $homepage
	 */
	public function setHomepage($homepage) {
		$this->getDm()->setHomepage($homepage);
		$this->_data['homepage'] = $homepage;
		return $this;
	}

	/**
	 * 设置QQ号码
	 * 
	 * @param stirng $qq
	 */
	public function setQq($qq) {
		$this->getDm()->setQq($qq);
		$this->_data['qq'] = $qq;
		return $this;
	}

	/**
	 * 设置msn
	 * 
	 * @param stirng $msn
	 */
	public function setMsn($msn) {
		$this->getDm()->setMsn($msn);
		$this->_data['msn'] = $msn;
		return $this;
	}

	/**
	 * 设置阿里旺旺号码
	 * 
	 * @param string $aliww
	 */
	public function setAliww($aliww) {
		$this->getDm()->setAliww($aliww);
		$this->_data['aliww'] = $aliww;
		return $this;
	}

	/**
	 * 设置手机号码
	 * 
	 * @param string $mobile
	 */
	public function setMobile($mobile) {
		$this->getDm()->setMobile($mobile);
		$this->_data['mobile'] = $mobile;
		return $this;
	}

	/**
	 * 设置手机号码验证码
	 * 
	 * @param string $mobileCode
	 */
	public function setMobileCode($mobileCode) {
		$this->_data['mobileCode'] = $mobileCode;
		return $this;
	}
	
	/**
	 * 设置支付帐号
	 * 
	 * @param string $alipay
	 */
	public function setAlipay($alipay) {
		$this->getDm()->setAlipay($alipay);
		$this->_data['alipay'] = $alipay;
		return $this;
	}
	
	/**** windid end ***/

	/** 
	 * 设置帐号状态
	 *
	 * @param int $status
	 * @return PwUserInfoDm
	 */
	public function setStatus($status) {
		$this->_data['status'] = $status;
		return $this;
	}
	
	/**
	 * 设置是否是为验证用户
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setUncheck($bool) {
		$this->_bitData['status'][PwUser::STATUS_UNCHECK] = (bool)$bool;
		return $this;
	}

	/**
	 * 设置是否是未激活用户
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setUnactive($bool) {
		$this->_bitData['status'][PwUser::STATUS_UNACTIVE] = (bool)$bool;
		return $this;
	}

	/**
	 * 设置用户被禁止头像
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setBanAvatar($bool) {
		$this->_bitData['status'][PwUser::STATUS_BAN_AVATAR] = (bool)$bool;
		return $this;
	}

	/**
	 * 用户被禁止签名
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setBanSign($bool) {
		$this->_bitData['status'][PwUser::STATUS_BAN_SIGN] = (bool)$bool;
		return $this;
	}

	/**
	 * 设置该用户是否允许使用UBB
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setSignUseubb($bool) {
		$this->_bitData['status'][PwUser::STATUS_SIGN_USEUBB] = (bool)$bool;
		return $this;
	}
	
	/**
	 * 设置该用户是否后后台权限
	 *
	 * @param boolean $bool
	 * @return PwUserInfoDm
	 */
	public function setAllowLoginAdmin($bool) {
		$this->_bitData['status'][PwUser::STATUS_ALLOW_LOGIN_ADMIN] = (bool)$bool;
		return $this;
	}
	
	/** 
	 * 设置真是姓名
	 *
	 * @param string $realname
	 * @return PwUserInfoDm
	 */
	public function setRealname($realname) {
		$this->getDm()->setRealname($realname);
		$this->_data['realname'] = $realname;
		return $this;
	}
	
	/**
	 * 设置个人简介
	 *
	 * @param string $profile
	 */
	public function setProfile($profile) {
		$this->getDm()->setProfile($profile);
		$this->_data['profile'] = $profile;
		return $this;
	}
	
	/** 
	 * 设置帖子签名
	 *
	 * @param string $bbs_sign
	 * @return PwUserInfoDm
	 */
	public function setBbsSign($bbs_sign) {
		$this->_data['bbs_sign'] = $bbs_sign;
		return $this;	
	}
	
	/** 
	 * 设置最后登录时间
	 *
	 * @param int $lastvisit
	 */
	public function setLastvisit($lastvisit) {
		$this->_data['lastvisit'] = $lastvisit;
		return $this;
	}
	
	/** 
	 * 设置最后登录IP
	 *
	 * @param string $lastloginip
	 */
	public function setLastloginip($lastloginip) {
		$this->_data['lastloginip'] = $lastloginip;
		return $this;
	}

	public function setLastActiveTime($time) {
		$this->_data['lastactivetime'] = $time;
		return $this;
	}

	public function setLastpost($lastpost) {
		$this->_data['lastpost'] = $lastpost;
		return $this;
	}
	
	/**
	 * 添加用户的帖子数
	 *
	 * @param int $num
	 * @return PwUserInfoDm
	 */
	public function addPostnum($num) {
		$this->_increaseData['postnum'] = intval($num);
		return $this;
	}

	/**
	 * 设置用户的帖子数
	 *
	 * @param int $num
	 * @return PwUserInfoDm
	 */
	public function setPostnum($num) {
		$this->_data['postnum'] = intval($num);
		return $this;
	}
	
	/**
	 * 添加用户的精华数
	 *
	 * @param int $num
	 * @return PwUserInfoDm
	 */
	public function addDigest($num) {
		$this->_increaseData['digest'] = intval($num);
		return $this;
	}
	
	/**
	 * 设置用户的精华数
	 *
	 * @param int $num
	 * @return PwUserInfoDm
	 */
	public function setDigest($num) {
		$this->_data['digest'] = intval($num);
		return $this;
	}

	public function addTodaypost($num) {
		$this->_increaseData['todaypost'] = intval($num);
		return $this;
	}

	public function setTodaypost($num) {
		$this->_data['todaypost'] = intval($num);
		return $this;
	}

	public function addTodayupload($num) {
		$this->_increaseData['todayupload'] = intval($num);
		return $this;
	}

	public function setTodayupload($num) {
		$this->_data['todayupload'] = intval($num);
		return $this;
	}

	/**
	 * 增加关注统计数
	 *
	 * @param int $follow 增加的关注数
	 */
	public function addFollows($follows) {
		$this->_increaseData['follows'] = intval($follows);
		return $this;
	}

	/**
	 * 增加粉丝统计数
	 *
	 * @param int $fans 增加的粉丝数
	 */
	public function addFans($fans) {
		$this->_increaseData['fans'] = intval($fans);
		return $this;
	}
	
	/**
	 * 设置未读通知数
	 * 
	 * @param int $notices
	 */
	public function setNoticeCount($notices){
		$this->_data['notices'] = intval($notices);
		return $this;
	}
	
	/**
	 * 设置未读消息数
	 * 
	 * @param int $messages
	 */
	public function setMessageCount($messages){
		$this->getDm()->setMessageCount($messages);
		$this->_data['messages'] = intval($messages);
		return $this;
	}
	
	/**
	 * 设置在线时间
	 *
	 * @param int $online
	 */
	public function setOnline($online) {
		$this->_data['onlinetime'] = intval($online);
		return $this;
	}

	/**
	 * 增加在线时间
	 *
	 * @param int $online
	 */
	public function addOnline($online) {
		$this->_increaseData['onlinetime'] = intval($online);
		return $this;
	}
	
	/**
	 * 设置尝试密码的次数记录
	 *
	 * @param string $trypwd
	 * @return PwUserInfoDm
	 */
	public function setTrypwd($trypwd) {
		$this->_data['trypwd'] = $trypwd;
		return $this;
	}
	
	/**
	 * 设置找回密码的次数记录
	 *
	 * @param string $findwd
	 * @return PwUserInfoDm
	 */
	public function setFindpwd($findwd) {
		$this->_data['findpwd'] = $findwd;
		return $this;
	}
	
	/**
	 * 上次发帖内容hash串
	 *
	 * @param string $str hash串
	 */
	public function setPostcheck($str) {
		$this->_data['postcheck'] = $str;
		return $this;
	}
	
	/** 
	 * 设置用户普通组的等级
	 *
	 * @param int $memberid
	 */
	public function setMemberid($memberid) {
		$this->_data['memberid'] = intval($memberid);
		return $this;
	}
	
	/** 
	 * 设置注册原因
	 *
	 * @param string $regreason
	 * @return PwUserInfoDm
	 */
	public function setRegreason($regreason) {
		$this->_data['regreason'] = $regreason;
		return $this;
	}
	
	/** 
	 * 设置固定电话号码
	 *
	 * @param string $phone
	 * @return PwUserInfoDm
	 */
	public function setTelphone($phone) {
		$this->_data['telphone'] = $phone;
		return $this;
	}
	
	/** 
	 * 设置通信地址
	 *
	 * @param string $address
	 * @return PwUserInfoDm
	 */
	public function setAddress($address) {
		$this->_data['address'] = $address;
		return $this;
	}
	
    /** 
     * 设置邮政变面
     *
     * @param string $zipcode
     * @return PwUserInfoDm
     */
    public function setZipcode($zipcode) {
    	$this->_data['zipcode'] = $zipcode;
    	return $this;
    }
    
    /* (non-PHPdoc)
     * @see PwBaseDm::getField()
     */
    public function getField($field) {
		if ($this->dm && ($result = $this->dm->getField($field)) !== null) {
			return $result;
		}
		return parent::getField($field);
    }
	
	/** 
	 * 设置消息提醒声音
	 *
	 * @param int $message_tone
	 * @return PwUserInfoDm
	 */
	public function setMessage_tone($message_tone) {
		$this->_data['message_tone'] = $message_tone;
		return $this;
	}
	
	/**
	 * 设置用户喜欢统计
	 */
	public function setLikes($number) {
		$this->_data['likes'] = intval($number);
		return $this;
	}
	
	/**
	 * 设置用户资料隐私设置
	 */
	public function setSecret($secrets) {
		$this->_data['secret'] = serialize($secrets);
		return $this;
	}
	
	/**
	 * 设置打卡信息
	 */
	public function setPunch($punch) {
		$this->_data['punch'] = serialize($punch);
		return $this;
	}
	
	/**
	 * 设置加入板块
	 */
	public function setJoinForum($join_forum) {
		$this->_data['join_forum'] = $join_forum;
		return $this;
	}
	
	/**
	 * 设置可能感兴趣的人
	 */
	public function setRecommendFriend($recommend_friend) {
		$this->_data['recommend_friend'] = $recommend_friend;
		return $this;
	}
	
	/**
	 * 设置最后积分变动的日志
	 *
	 * @param string $log
	 * @return PwUserInfoDm
	 */
	public function setLastCreditAffectLog($log) {
		$this->_data['last_credit_affect_log'] = $log;
		return $this;
	}
	
	/**
	 * 勋章ID
	 *
	 * @param array $medalids 
	 */
	public function setMedalIds($medalids) {
		$this->_data['medal_ids'] = implode(',', $medalids);
		return $this;
	}
	
	/**
	 * 更新通知数
	 * 
	 * @param int $num
	 */
	public function addNotice($num) {
		$this->_increaseData['notices'] = intval($num);
		return $this;
	}
	
	/**
	 * 更新消息数
	 * 
	 * @param int $num
	 */
	public function addMessages($num) {
		$this->getDm()->addMessages($num);
		$this->_increaseData['messages'] = intval($num);
		return $this;
	}
	
	/**
	 * 更新用户组信息
	 */
	public function getUserBelongs() {
		return $this->_belong; 
	}
	
	/* (non-PHPdoc)
	 * @see WindidUserDm::beforeUpdate()
	 */
	protected function _beforeUpdate() {
		if (0 >= $this->uid) return new PwError('USER:illegal.id');
		if ($this->dm && WINDID_CONNECT == 'db' && ($result =  $this->dm->beforeUpdate()) !== true) {
			return $this->_getWindidMsg($result);
		}
		if (true !== ($result = $this->check())) return $result;
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see WindidUserDm::beforeAdd()
	 */
	protected function _beforeAdd() {
		if (!$this->dm) {
			return new PwError('USER:user.info.error');
		}
		if (($result = PwUserValidator::isUsernameHasIllegalChar($this->getField('username'))) !== false) {
			return $result;
		}
		if (($result = PwUserValidator::isPwdValid($this->_password, $this->getField('username'))) !== true) {
			return $result;
		}
		if (WINDID_CONNECT == 'db' && ($result = $this->dm->beforeAdd()) !== true) {
			return $this->_getWindidMsg($result);
		}
		if (true !== ($result = $this->check())) return $result;
		return true;
	}
	
	/** 
	 * 检查转换数据
	 * 
	 * @return boolean|PwError 
	 */
	protected function check() {
		if ($this->_data['groups']) {
			$this->_data['groups'] = implode(',', $this->_data['groups']);
		}

		//【用户资料验证】手机号码格式验证
		if (($_tmp = $this->getField('mobile')) && (true !== ($r = PwUserValidator::isMobileValid($_tmp)))) {
			return $r;
		}
		//【用户资料验证】固定电话号码格式验证
		if (($_tmp = $this->getField('telphone')) && (true !== ($r = PwUserValidator::isTelPhone($_tmp)))) {
			return $r;
		}
		//【用户资料验证】邮编格式验证
		if (($_tmp = $this->getField('zipcode')) && (false === WindValidator::isZipcode($_tmp))) {
			return new PwError('USER:error.zipcode');
		}
		//【用户资料验证】个人主页长度限制
		if (($_tmp = $this->getField('homepage')) && (false === WindValidator::isUrl($_tmp) || true === WindValidator::isLegalLength($_tmp, 200))) {
			return new PwError('USER:error.homepage');
		}
		//【用户资料验证】自我简介长度限制
		if (($_tmp = $this->getField('profile')) && (true === WindValidator::isLegalLength($_tmp, 250))) {
			return new PwError('USER:error.profile.length', array('{length}' => 250));
		}
		
		//TODO【用户资料验证】BBS签名验证长度判断----后台权限设置
		/*
		if (($_tmp = $this->getField('bbs_sign')) && (true === WindValidator::isLegalLength($_tmp, 500))) {
			return new PwError('USER:error.bbs_sign.length', array('{length}' => 500));
		}
		*/
		return true;
	}

	protected function _getWindidMsg($result) {
		$errorCode = $result->getCode();
		$var = array();
		if ($errorCode == -2) {
			$config = WindidApi::C('reg');
			$var = array('{min}' => $config['security.username.min'], '{max}' => $config['security.username.max']);
		}
		if ($errorCode == -11) {
			$config = WindidApi::C('reg');
			$var = array('{min}' => $config['security.password.min'], '{max}' => $config['security.password.max']);
		}
		return new PwError('WINDID:code.' . $errorCode, $var);
	}
}