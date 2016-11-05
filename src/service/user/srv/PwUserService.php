<?php
Wind::import('SRV:user.dm.PwUserInfoDm');
/**
 * 用户的业务接口
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserService.php 24736 2013-02-19 09:24:40Z jieyin $
 * @package src.service.user.srv
 */
class PwUserService {

	/** 
	 * 验证用户
	 * 
	 * 返回错误码 PwError->getError():
	 * <ul>
	 * <li>-1: 用户不存在</li>
	 * <li>-2: 用户密码错误</li>
	 * <li>-3: 用户安全问题回答不正确</li>
	 * </ul>
	 * 
	 * @param string $username 用户
	 * @param string $password 用户密码
	 * @param int $type 验证类型 <1：用户id, 2: 用户名, 3：用户email>
	 * @param string $question 安全问题
	 * @param string $answer   安全问题的答案
	 * @return PwError|array
	 */
	public function verifyUser($username, $password, $type = 1, $question = null, $answer = null) {
		$checkSafe = is_null($question) && is_null($answer) ? false : true;
		$result = $this->_getWindidUser()->login($username, $password, $type, $checkSafe, $question, $answer);
		switch ($result[0]) {
			case 1://用户信息正常
				return $result[1];
			case -14://用户不存在
				return new PwError('USER:verify.error.name');
			case -13://用户密码错误
				return new PwError('USER:verify.error.pwd');
			case -20://用户安全问题错误
				return new PwError('USER:verify.error.question');
		}
	}

	/** 
	 * 退出系统
	 * 
	 * @return boolean
	 */
	public function logout() {
		$loginUser = Wekit::getLoginUser();
		PwSimpleHook::getInstance('PwUserService_logout')->runDo($loginUser);
		$loginUser->reset();
		return Pw::setCookie('winduser', '', -1);
	}
	
	/** 
	 * 根据用户拥有组信息获得该用户的当前使用的用户身份
	 * <pre>
	 * 用户拥有的组的使用顺序：
	 * <ol>
	 * <li>系统组 》荣誉组 》会员组</li>
	 * <li>然后按组内的id顺序进行变更。</li>
	 * </ol>
	 * </pre>
	 *
	 * @param int $groupid 用户当前用户组
	 * @param array $userGroups 用户拥有的组
	 * @return array (gid, groups)
	 */
	public function caculateUserGroupid($groupid, $userGroups) {
		$banGids = array(1, 2, 6, 7);
		// 如果是游客/禁止发言/未验证用户组则不变动该用户组
		if (in_array($groupid, $banGids)) return array($groupid, array());
		//【当前用户组】 当前用户组设置顺序:系统组/特殊组/普通组
		$groupQue = array('system', 'special');
		$temp = array();
		$time = Pw::getTime();
		foreach ($userGroups as $_gid => $_time) {
			if ($_time == 0 || $_time > $time) {
				$temp[$_gid] = $_time;
			}
		}
		$gid = 0;
		if ($temp) {
			/* @var $groupDs PwUserGroups */
			$groupDs = Wekit::load('usergroup.PwUserGroups');
			$groups = $groupDs->getClassifiedGroups();
			$userGids = array_keys($temp);
			foreach ($groupQue as $_tmpType) {
				/*如果用户拥有系统组或是特殊组，根据用户组ID从小到大排序的第一个用户组为用户当前显示的组*/
				$_tmp = array_intersect(array_keys($groups[$_tmpType]), $userGids);
				if ($_tmp) {
					ksort($_tmp);
					$gid = array_shift($_tmp);
					break;
				}
			}
		}
		//当前用户组过期
		if (!$temp[$groupid]) {
			$groupid = $gid;
		}
		return array($groupid, $temp);
	}

	/** 
	 * 创建登录用户标识
	 *
	 * @param int $uid 用户ID
	 * @param string $password 用户密码
	 * @param int $rememberme 是否采用记住当前用户，记住则保存1年
	 * @return boolean
	 */
	public function createIdentity($uid, $password, $rememberme = 0) {
		$identity = Pw::encrypt($uid . "\t" . Pw::getPwdCode($password));
		return Pw::setCookie('winduser', $identity, $rememberme ? 31536000 : NULL);
	}

	/** 
	 * 更新用户登录信息
	 *
	 * @param int $uid 用户ID
	 * @param string $ip 用户IP
	 * @return boolean
	 */
	public function updateLastLoginData($uid, $ip) {
		$dm = new PwUserInfoDm($uid);
		$dm->setLastvisit(Pw::getTime())->setLastloginip($ip);
		return $this->_getUserDs()->editUser($dm, PwUser::FETCH_DATA);
	}
	
	/**
	 * 判断该用户是否有设置安全问题
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function isSetSafecv($uid) {
		$info = WindidApi::api('user')->getUser($uid, 1);
		return !empty($info['safecv']);
	}
	
	/** 
	 * 判断用户是否必须设置安全问题
	 * 如果当前用户所有拥有的组(包括附加组)中有一个组是必须要设置安全问题和答案的，则该用户必须设置安全问题和答案
	 * 
	 * @param int $uid 用户ID
	 * @return boolean
	 */
	public function mustSettingSafeQuestion($uid) {
		$groups = $this->getGidsByUid($uid);
		$mustSettingGroups = Wekit::C('login', 'question.groups');
		return !$mustSettingGroups ? false : (array_intersect($groups, $mustSettingGroups) ? true : false);
	}
	
	/**
	 * 根据用户ID获得该用户拥有的用户组
	 *
	 * @param int $uid 
	 * @return array
	 */
	public function getGidsByUid($uid) {
		if (!$uid) return array();
		$info = Wekit::load('user.PwUser')->getUserByUid($uid, PwUser::FETCH_MAIN);
		return array_merge(explode(',', $info['groups']), array($info['groupid'], $info['memberid']));
	}
	
	/**
	 * 还原头像
	 *
	 * @param int $uid
	 * @param string $type 还原类型-一种默认头像face*,一种是禁止头像ban*
	 * @return boolean
	 */
	public function restoreDefualtAvatar($uid, $type = 'face') {
		$result =  $this->_getWindidAvatar()->defaultAvatar($uid, $type);
		if ($result < 1) return false;
		return true;
	}
	
	/**
	 * 获得安全问题
	 * 
	 * @param int|null $id
	 * @return array
	 */
	public function getSafeQuestion($id = null) {
		$qList = array(
			1 => '我爸爸的出生地',
			2 => '我妈妈的出生地',
			3 => '我的小学校名',
			4 => '我的中学校名',
			5 => '我最喜欢的运动',
			6 => '我最喜欢的歌曲',
			7 => '我最喜欢的电影',
			8 => '我最喜欢的颜色'
		);
		return is_null($id) ? $qList : (isset($qList[$id]) ? $qList[$id] : $id);
	}
	
	/** 
	 * 获得用户Ds
	 *
	 * @return PwUser
	 */
	private function _getUserDs() {
		return Wekit::load('user.PwUser');
	}
	
	/** 
	 * 获得用户组Ds
	 *
	 * @return PwUserGroups
	 */
	private function _getUserGroupDs() {
		return Wekit::load('usergroup.PwUserGroups');
	}

	/** 
	 * 获得windidUser
	 *
	 * @return WindidUserApi
	 */
	private function _getWindidUser() {
		return WindidApi::api('user');
	}
	
	private function _getWindidAvatar() {
		return WindidApi::api('avatar');
	}
}
