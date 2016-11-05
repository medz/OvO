<?php
Wind::import('SRV:user.PwUserBan');

/**
 * 用户Ban的BP
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwBanBp.php 23904 2013-01-17 05:27:48Z xiaoxia.xuxx $
 * @package src.service.user.srv
 */
class PwBanBp {
	private $uid = 0;
	
	private $banInfo = array();
	private $endCallBack = array();
	private $banType = array();
	/**
	 * 构造函数
	 *
	 * @param int $uid
	 */
	public function __construct($uid) {
		$this->uid = intval($uid);
		$this->banType = Wekit::load('user.srv.PwUserBanService')->getBanType();
		foreach ($this->banType as $_k => $_value) {
			$this->banInfo[$_k] = array(false, array());
		}
	}
	
	/**
	 * 初始化所有的禁止信息
	 */
	public function checkIfBan() {
		$banInfo = $this->_getDs()->getBanInfo($this->uid);
		if (!$banInfo) return false;
		foreach ($this->banType as $_k => $_value) {
			$this->banInfo[$_k][0] = true;
		}
		foreach ($banInfo as $key => $_one) {
			$this->banInfo[$_one['typeid']][1][] = $_one;
		}
		return true;
	}
	
	/**
	 * 检查是否有禁止信息
	 * 
	 * @return boolean
	 */
	public function checkIfBanSpeak() {
		return $this->_checkIfBan(PwUserBan::BAN_SPEAK);
	}
	
	/**
	 * 解除禁止
	 * 
	 * @return false|mixed
	 */
	public function endDateTimeBanSpeak() {
		return $this->_endDateTimeBanSpeak(PwUserBan::BAN_SPEAK);
	}
	
	/**
	 * 调取解除禁止发言之后的返回结果
	 *
	 * @return mixed
	 */
	public function callEndDateTimeBanSpeak() {
		return $this->_callEndDateTimeBan(PwUserBan::BAN_SPEAK);
	}
	
	/**
	 * 获得禁止信息
	 * 
	 * @return array
	 */
	public function getBanSpeakInfo() {
		return $this->_getBanInfo(PwUserBan::BAN_SPEAK);
	}
	
	/**
	 * 检查是否有禁止签名信息
	 *
	 * @return boolean
	 */
	public function checkIfBanSign() {
		return $this->_checkIfBan(PwUserBan::BAN_SIGN);
	}
	
	/**
	 * 解除禁止签名
	 *
	 * @return false|mixed
	 */
	public function endDateTimeBanSign() {
		return $this->_endDateTimeBanSpeak(PwUserBan::BAN_SIGN);
	}
	
	/**
	 * 调取解除禁止签名之后的返回结果
	 *
	 * @return mixed
	 */
	public function callEndDateTimeBanSign() {
		return $this->_callEndDateTimeBan(PwUserBan::BAN_SIGN);
	}
	
	/**
	 * 获得禁止签名信息
	 *
	 * @return array
	 */
	public function getBanSignInfo() {
		return $this->_getBanInfo(PwUserBan::BAN_SIGN);
	}
	
	/**
	 * 检查是否有禁止头像信息
	 *
	 * @return boolean
	 */
	public function checkIfBanAvatar() {
		return $this->_checkIfBan(PwUserBan::BAN_AVATAR);
	}
	
	/**
	 * 解除禁止头像
	 *
	 * @return false|mixed
	 */
	public function endDateTimeBanAvatar() {
		return $this->_endDateTimeBanSpeak(PwUserBan::BAN_AVATAR);
	}
	
	/**
	 * 调取解除禁止头像之后的返回结果
	 *
	 * @return mixed
	 */
	public function callEndDateTimeBanAvatar() {
		return $this->_callEndDateTimeBan(PwUserBan::BAN_AVATAR);
	}
	
	/**
	 * 获得禁止头像信息
	 *
	 * @return array
	 */
	public function getBanAvatarInfo() {
		return $this->_getBanInfo(PwUserBan::BAN_AVATAR);
	}
	
	/**
	 * 如果用户没有禁止发言记录，但是用户组ID确实禁止发言用户组，则将用户信息进行修复
	 * 
	 * @return mixed
	 */
	public function recoveryBanSpeaKError() {
		return $this->_recoverBanError(PwUserBan::BAN_SPEAK);
	}
	
	/**
	 * 如果用户没有禁止签名记录，但是用户状态确实有禁止签名标签，将状态修复
	 *
	 * @return mixed
	 */
	public function recoveryBanSignError() {
		return $this->_recoverBanError(PwUserBan::BAN_SIGN);
	}
	
	/**
	 * 如果用户没有禁止头像记录，但是用户状态确实有禁止头像标签，将状态修复
	 *
	 * @return mixed
	 */
	public function recoveryBanAvatarError() {
		return $this->_recoverBanError(PwUserBan::BAN_AVATAR);
	}
	
	/**
	 * 禁止信息的错误修复
	 *
	 * @param int $type
	 * @return boolean|mixed
	 */
	private function _recoverBanError($type = PwUserBan::BAN_SPEAK) {
// 		if ($this->_getBanInfo($type)) return false;
		$class = Wekit::load($this->banType[$type]['class']);
		return call_user_func_array(array($class, 'deleteBan'), array($this->uid));
	}
	
	/**
	 * 判断莫类是否被禁止
	 *
	 * @param int $type
	 * @return boolean
	 */
	private function _checkIfBan($type = PwUserBan::BAN_SPEAK) {
		if (!array_key_exists($type, $this->banType)) return false;
		if (false === $this->banInfo[$type][0]) {
			$this->banInfo[$type][0] = true;
			$this->banInfo[$type][1] = $this->_getDs()->getBanInfoByTypeid($this->uid, $type);
		}
		return empty($this->banInfo[$type][1]) ? false : true;
	}
	
	/**
	 * 解除某类禁止类型的有时间期限的禁止
	 *
	 * @param int $type
	 * @return boolean
	 */
	private function _endDateTimeBanSpeak($type = PwUserBan::BAN_SPEAK) {
		if (false === $this->_checkIfBan($type)) return true;
		$t = $this->_autoEndBan($type);
		return $t ? (false === $this->_checkIfBan($type) ? true : false) : false; 
	}
	
	/**
	 * 获得某类禁止记录
	 *
	 * @param int $type
	 * @return array
	 */
	private function _getBanInfo($type = PwUserBan::BAN_SPEAK) {
		$this->_endDateTimeBanSpeak($type);
		reset($this->banInfo[$type][1]);
		return current($this->banInfo[$type][1]);
	}
	
	/**
	 * 获得某类禁止被解除之后返回的信息
	 *
	 * @param int $type
	 * @return mixed
	 */
	private function _callEndDateTimeBan($type = PwUserBan::BAN_SPEAK) {
		return isset($this->endCallBack[$type]) ? $this->endCallBack[$type] : null;
	}
	
	/**
	 * 解除到期的禁止记录
	 *
	 * @param array $banInfo
	 * @return mixed
	 */
	private function _autoEndBan($type = PwUserBan::BAN_SPEAK) {
		$_notice = $clear = array();
		$now = Pw::getTime();
		foreach ($this->banInfo[$type][1] as $_key => $item) {
			if (($item['end_time'] != 0) && $item['end_time'] < $now) {
				$clear[] = $item['id'];
				//操作相关类型的后续操作
				$class = Wekit::load($this->banType[$item['typeid']]['class']);
				$this->endCallBack[$item['typeid']] = call_user_func_array(array($class, 'deleteBan'), array($item['uid']));
		
				$_notice['end_time'] = $item['end_time'];
				$_notice['reason'] = $item['reason'];
				$_notice['type'][] = $item['typeid'];
				$_notice['operator'] = 'system';
				unset($this->banInfo[$type][1][$_key]);
			}
		}
		$isEnd = false;
		if ($clear) {
			$isEnd = true;
			$this->_getDs()->batchDelete($clear);
			//【自动解禁】发送消息
			/* @var $banService PwUserBanService */
			$banService = Wekit::load('user.srv.PwUserBanService');
			$banService->sendNotice(array($this->uid => $_notice), 3);
		}
		return $isEnd;
	}
	
	/**
	 * 获得用户禁止
	 *
	 * @return PwUserBan
	 */
	private function _getDs() {
		return Wekit::load('user.PwUserBan');
	}
}