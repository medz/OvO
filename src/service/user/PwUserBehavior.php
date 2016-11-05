<?php
/**
 * 用户行为服务
 * 
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBehavior.php 23666 2013-01-14 08:34:43Z jinlong.panjl $ 
 * @package 
 */
class PwUserBehavior {
	
	/**
	 * 获取用户的单一行为记录
	 * 
	 * @param int $uid
	 * @param string $behavior
	 */
	public function getBehavior($uid, $behavior) {
		if (!$uid) return array();
		$info = $this->_getdao()->getInfo($uid, $behavior);
		if (!$info) return array();
		$time = Pw::getTime();
		if($info['expired_time'] > 0 && $info['expired_time'] < $time) $info['number'] = 0;
		return $info;
	}
	
	/**
	 * 获取多个用户的所有行为
	 * 
	 * @param array $uids
	 */
	public function fetchBehavior($uids) {
		if (!is_array($uids) || count($uids) < 1) return array();
		return $this->_getdao()->fetchInfo($uids);
	}
	
	/**
	 * 获取一个用户的所有行为
	 * 
	 * @param int $uid
	 */
	public function getBehaviorList($uid) {
		$uid = (int)$uid;
		if ($uid < 1) return array();
		return $this->_getdao()->getBehaviorList($uid);
	}
	
	/**
	 * 用户连续天数的行为记录&&用户累计行为记录
	 * 
	 * @param int $uid
	 * @param string $behavior 行为标记
	 * @param int $time 当前时间，为0则为累计行为记录,否则为连续行为记录(每天)
	 */
	public function replaceBehavior($uid, $behavior, $time = 0) {
		if ($uid < 1 || !$behavior) return false;
		$expired = $yesterday = 0;
		$number = 1;
		if ($time > 0) {
			$yesterday = Pw::str2time(Pw::time2str($time,'Y-m-d'));
			$expired = $yesterday + 86400*2;
		}
		$info = $this->getBehavior($uid, $behavior);
		if ($info) {
			$_time = (int)$info['extend_info'];
			$number = (int)$info['number'];
			if ($yesterday > 0 && $_time >= $yesterday) {
				return false;
			} elseif ($yesterday > 0 && $_time >= ($yesterday - 86400) ) {
				$number ++;
			} elseif ($yesterday > 0 && $_time < ($yesterday - 86400)) {
				$number	= 1;
			} else {  // 累计行为记录
				$number++;
			}
		}
		$data['uid'] = $uid;
		$data['behavior'] = $behavior;
		$data['number'] = $number;
		$data['extend_info'] = $time;
		$data['expired_time'] = $expired;
		if ($this->_getdao()->replaceInfo($data)) return $number;
	}
	
	/**
	 * 用户(每天)的行为,次日重新计数
	 * 
	 * @param int $uid
	 * @param string $behavior
	 * @param int $time
	 */
	public function replaceDayBehavior($uid, $behavior, $time) {
		if ($uid < 1 || !$behavior || $time < 0) return false;
		$number = 1;
		$yesterday = Pw::str2time(Pw::time2str($time,'Y-m-d'));
		$expired = $yesterday + 86400;
		$info = $this->getBehavior($uid, $behavior);
		if ($info) {
			$_time = (int)$info['extend_info'];
			$number = (int)$info['number'];
			if ($_time >= $yesterday) {
				$number ++;
			} else { 
				$number = 1;
			}
		}
		$data['uid'] = $uid;
		$data['behavior'] = $behavior;
		$data['number'] = $number;
		$data['extend_info'] = $time;
		$data['expired_time'] = $expired;
		if ($this->_getdao()->replaceInfo($data)) return $number;
	}
	
	/**
	 * 用户(每天)的行为,次日重新计数 | $num幅度
	 * 
	 * @param int $uid
	 * @param string $behavior
	 * @param int $time
	 */
	public function replaceDayNumBehavior($uid, $behavior, $time, $num = 1) {
		if ($uid < 1 || !$behavior || $time < 0) return false;
		$number = $num ? $num : 1;
		$yesterday = Pw::str2time(Pw::time2str($time,'Y-m-d'));
		$expired = $yesterday + 86400;
		$info = $this->getBehavior($uid, $behavior);
		if ($info) {
			$_time = (int)$info['extend_info'];
			$number = (int)$info['number'];
			if ($_time >= $yesterday) {
				$number = $number + $num;
			}
		}
		$data['uid'] = $uid;
		$data['behavior'] = $behavior;
		$data['number'] = $number;
		$data['extend_info'] = $time;
		$data['expired_time'] = $expired;
		if ($this->_getdao()->replaceInfo($data)) return $number;
	}
	
	/**
	 * 单纯记录key-value信息
	 * 
	 * @param int $uid
	 * @param string $behavior
	 * @param int $number
	 * @param string $extend
	 */
	public function replaceInfo($uid, $behavior, $number = 0 ,$extend = '') {
		if ($uid < 1 || !$behavior) return false;
		$data = array();
		$data['uid'] = $uid;
		$data['behavior'] = $behavior;
		$data['number'] = $number;
		$data['extend_info'] = $extend;
		return $this->_getdao()->replaceInfo($data);
	}
	
	public function deleteInfo($uid) {
		if (!$uid) return false;
		return $this->_getdao()->deleteInfo($uid);
	}
	
	public function deleteInfoByUidBehavior($uid, $behavior) {
		if (!$uid || !$behavior) return false;
		return $this->_getdao()->deleteInfoByUidBehavior($uid, $behavior);
	}
	
	private function _getdao() {
		return Wekit::loadDao('user.dao.PwUserBehaviorDao');
	}
}