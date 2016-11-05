<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMedalService.php 22364 2012-12-21 12:32:59Z xiaoxia.xuxx $ 
 * @package 
 */
class PwMedalService {
 	
 	/**
	 * 从勋章用户表获取一个用户的勋章
	 * 
	 * 全局缓存从pwMedalCahce.php里取
	 * @param int $uid
	 */
	public function getUserMedal($uid) {
		$_medals = array();
		$userMedal = $this->_getMedalUserDs()->getMedalUser($uid);
		if (!$userMedal) return array();
		$medalIds = empty($userMedal['medals']) ?  array() : explode(',', $userMedal['medals']);
		$medalIds = array_unique($medalIds);
		$medals = $this->_getMedalDs()->fetchMedalInfo($medalIds);
		foreach ($medalIds AS $medalId) {
			if (!$medals[$medalId]) continue;
			$medals[$medalId]['image'] = $this->getMedalImage($medals[$medalId]['path'],$medals[$medalId]['image']);
			$medals[$medalId]['icon'] = $this->getMedalImage($medals[$medalId]['path'],$medals[$medalId]['icon']);
			$_medals[] = $medals[$medalId];
		}

		return $_medals;
	}
	
  	/**
	 * 从勋章用户表获取多个用户的勋章
	 * 
	 * 全局缓存从pwMedalCahce.php里取
	 * @param array $uid
	 */
	public function fetchUserMedal($uids) {
		if (!is_array($uids)) return array();
		$_userMedalIds = $_allMedalId = $_medals = array();
		$userMedal = $this->_getMedalUserDs()->fetchMedalUser($uids);
		if (!$userMedal) return array();
		
		foreach ($uids AS $uid) {
			$_userMedalIds[$uid] = !$userMedal[$uid]['medals'] ?  array() : explode(',', $userMedal[$uid]['medals']);
			$_allMedalId = array_merge($_allMedalId, $_userMedalIds[$uid]);
		}
		$_allMedalId = array_unique($_allMedalId);
		$medals = $this->_getMedalDs()->fetchMedalInfo($_allMedalId);
		$attachUrl =Pw::getPath(''). 'medal/';
		$localUrl = WindUrlHelper::checkUrl(PUBLIC_RES . '/images/medal/', PUBLIC_URL) . '/' ;
		foreach ($_userMedalIds AS $uid=>$medalIds) {
			$_medalInfo = array();
			foreach ($medalIds AS $medalId) {
				if (!$medals[$medalId]) continue;
				$path = $medals[$medalId]['path'] ?  $attachUrl : $localUrl;
				$_tmp = $medals[$medalId];
				$_tmp['image'] = $path .  $_tmp['image'];
				$_tmp['icon'] = $path .  $_tmp['icon'];
				$_medalInfo[] = $_tmp;
			}
			$_medals[$uid] = $_medalInfo;
		}
		return $_medals;
	}
 	
 	/**
 	 * 勋章领取，用于申请任务的勋章
 	 * 
 	 * @param int $logId
 	 * @param int $uid
 	 */
 	public function awardMedal($logId, $uid) {
 		$log = $this->_getMedalLogDs()->getMedalLog($logId);
		if (!isset($log['uid']) || $log['uid'] != $uid || $log['award_status'] != 3) return new PwError('MEDAL:info.error');
 		$medal = $this->_getMedalDs()->getMedalInfo($log['medal_id']);
		$time = Pw::getTime();
 		$expired = ($medal['expired_days'] > 0) ? ($time + $medal['expired_days']*86400) : 0;
		Wind::import('SRV:medal.dm.PwMedalLogDm');
 		$dm = new PwMedalLogDm($logId);
 		$dm->setAwardStatus(4)
 			->setExpiredTime($expired);
 		$resource = $this->_getMedalLogDs()->updateInfo($dm);
 		if ($resource instanceof PwError) return $resource->getError();
 		return $this->updateMedalUser($uid);
 		//return $this->sendNotice($uid, $logId, $log['medal_id'], 6);
 	}
 	
	/**
	 * 颁发勋章，用于完成的自动任务获取的勋章
	 * 
	 * @param int $uid
	 * @param int $medalId
	 */
 	public function awardTaskMedal($uid, $medalId) {
 		$info = $this->_getMedalDs()->getMedalInfo($medalId);
 		if (!$info) return false;
 		$userLog = $this->_getMedalLogDs()->getInfoByUidMedalId($uid, $medalId);
 		Wind::import('SRV:medal.dm.PwMedalLogDm');
 		$time = Pw::getTime();
 		if ($userLog && $userLog['award_status'] < 4) {
 			$dm = new PwMedalLogDm($userLog['log_id']);
 			$dm->setAwardStatus(3);
 			$resource = $this->_getMedalLogDs()->updateInfo($dm);
 		} else {
 			$dm = new PwMedalLogDm();
 			$dm->setMedalid($medalId)
 				->setUid($uid)
 				->setAwardStatus(3)
 				->setCreatedTime($time);
 			$resource = $this->_getMedalLogDs()->replaceMedalLog($dm);
 		}
 	}
 	
 	/**
 	 * 勋章消息发送
 	 * 
 	 * @param int $uid
 	 * @param int $medelId
 	 * @param string $type    1.自动颁发|2.管理员颁发|3.申请通过|4.申请不通过|5.系统回收|6.领取勋章|7.管理员回收
 	 * @param string $reason
 	 */
 	public function sendNotice($uid, $logId, $medelId, $type = 1, $reason = '') {
 		$info = $this->_getMedalDs()->getMedalInfo($medelId);
 		if (!$info) return false;
 		$param = 0;
 		switch ($type) {
 			case 1:
 			case 2:
 			case 3:
 			case 4:
 			case 6:
 			case 7:
 				$extendParams = array( 'logid'=>$logId, 'name'=>$info['name'], 'medelId'=>$medelId, 'type'=>$type, 'reason'=>$reason);
 				return Wekit::load('SRV:message.srv.PwNoticeService')->sendNotice($uid, 'medal', $param, $extendParams);
 			case 5:
 				$lang = Wind::getComponent('i18n');
 				$awardType = $this->awardTypes($info['award_type']);
 				if (!$reason) $reason = $info['receive_type'] == 1 ? '您的'.$lang->getMessage("MEDAL:awardtype.".$awardType) ."低于勋章设定值" .$info['award_condition'] : '';
 				$extendParams = array( 'logid'=>$logId, 'name'=>$info['name'], 'medelId'=>$medelId, 'type'=>$type, 'reason'=>$reason);
 				return Wekit::load('SRV:message.srv.PwNoticeService')->sendNotice($uid, 'medal', $param, $extendParams);
 		}
 	}
 	
 	/**
 	 * 停用勋章
 	 * 
 	 * @param int $logid
 	 */
 	public function stopAward($logid, $type = 5) {
 		if ($logid <1) return new PwError('info_error');
 		$info = $this->_getMedalLogDs()->getMedalLog($logid);
 		if (!$info) return new PwError('info_error');
 		$resource = $this->_getMedalLogDs()->deleteInfo($logid);
 		if (!$resource) return new PwError('info_error');
 		$this->sendNotice($info['uid'], $logid, $info['medal_id'], $type);
 		return $this->updateMedalUser($info['uid']);
 	}
 	
 	/**
 	 * 回收用户过期勋章
 	 * 
 	 */
 	public function recoverMedal($uid) {
		$time = Pw::getTime();
		$userMedalUser = $this->_getMedalUserDs()->getMedalUser($uid);
		if ($userMedalUser['expired_time'] > 0 && $userMedalUser['expired_time'] < $time) {
			$this->updateMedalUser($uid);
		}
	}
	
 	/**
 	 * 更新用户勋章统计
 	 * 
 	 * @param int $uid
 	 */
 	public  function updateMedalUser($uid) {
 		$expireds = $medalids = array();
 		$time = Pw::getTime();
 		$logs = $this->_getMedalLogDs()->getInfoListByUidStatus($uid, 4);
 		foreach ($logs AS $log) {
 			if ($log['expired_time'] > 0 && $log['expired_time'] < $time ){
 				$this->_getMedalLogDs()->deleteInfo($log['log_id']);
 				$this->sendNotice($uid, $log['log_id'], $log['medal_id'], 5);
 			} else {
 				$medalids[] = $log['medal_id'];
 				$log['expired_time'] > 0 &&$expireds[] = $log['expired_time'];
 				//$expired = $expired < $log['expired_time'] ? $log['expired_time'] : $expired;
 			}
 		}
 		$expireds = array_filter($expireds);
 		sort($expireds,SORT_NUMERIC);
 		$expired = array_shift($expireds);
 		
 		/*user_data冗余*/
 		$dm = Wind::import('SRV:user.dm.PwUserInfoDm');
 		$dm = new PwUserInfoDm($uid);
 		$dm->setMedalIds($medalids);
 		Wekit::load('user.PwUser')->editUser($dm, PwUser::FETCH_DATA);
 		/*end*/
 		
 		$dm = Wind::import('SRV:medal.dm.PwMedalUserDm');
 		$dm = new PwMedalUserDm($uid);
 		$dm->setMedals($medalids)
 			->setCounts(count($medalids))
 			->setExpiredTime($expired);
 		return $this->_getMedalUserDs()->replaceInfo($dm);
 	}
 	
 	/**
 	 * 勋章缓存更新策略
 	 *
	 * @return void
 	 */
 	public function updateCache() {
 		$cacheDs = Wekit::cache();
 		$cacheDs->set('medal_all', $this->getMedalAllCacheValue());
 		$cacheDs->set('medal_auto', $this->getMedalAutoCacheValue());
 	}
	
	/**
	 * 获取所有勋章缓存内容
	 *
	 * @return array
	 */
	public function getMedalAllCacheValue() {
		$medalAll = array();
		$all = $this->_getMedalDs()->getAllMedal();
 		foreach ($all AS $medal) {
 			$medalAll[$medal['medal_id']] = array(
 				'name'	=> $medal['name'],
 				'path'	=> $medal['path'],
 				'image'	=> $medal['image'],
				'icon'	=> $medal['icon'],
 			);
 		}
		return $medalAll;
	}

	/**
	 * 获取所有自动勋章缓存内容
	 *
	 * @return array
	 */
	public function getMedalAutoCacheValue() {
		$medalAuto = array();
		$auto = $this->_getMedalDs()->getInfoListByReceiveType(1, 1);
 		foreach ($auto AS $medal) {
 			$medalAuto[] = $medal['medal_id'];
 		}
		return $medalAuto;
	}
	
 	/**
 	 * 判断勋章用户组与用户组的领取权限
 	 * 
 	 * @param string $userGids   1,2,3,4
 	 * @param string $medalGids  1,2,3,4
 	 */
	public function allowAwardMedal($userGids, $medalGids = '') {
		$medalGids = !is_array($medalGids) && $medalGids ? explode(',', $medalGids) : $medalGids ;
		$userGids = !is_array($userGids) && $userGids ?  explode(',', $userGids) : $userGids;
		if ($medalGids && !array_intersect($userGids, $medalGids)) return false;
		return true;
	}
	
	public function getUserBehavior($uid) {
		$_array = array();
		$behaviors = Wekit::load('user.PwUserBehavior')->getBehaviorList($uid);
		$awardTypes = $this->awardTypes();
		foreach ($behaviors AS $behavior) {
			$_array[$behavior['behavior']] = $behavior['number'];
		}
		$statistics = Wekit::load('user.PwUser')->getUserByUid($uid, PwUser::FETCH_DATA);
		$_array['like_count'] = $statistics['likes'];
		$_array['follow_number'] = $statistics['follows'];
		$_array['fans_number'] = $statistics['fans'];
		return $_array;
	}
	
   	public function awardTypes($type = '') {
		$_array = array(
			1=>'login_days',
			2=>'post_days',
			3=>'thread_days',
			4=>'safa_times',
			5=>'fans_number',
			6=>'belike_times',
			7=>'thread_count',
			8=>'follow_number',
			9=>'like_count',
			10=>'login_count'
		);
		if (!empty($type)) return $_array[$type];
		return $_array;
	}
	
 	public function getMedalImage($path = '', $filename = '') {
 		if ($path) {
 			return Pw::getPath($path .  $filename);
 		} else {
 			return WindUrlHelper::checkUrl(PUBLIC_RES . '/images/medal/', PUBLIC_URL) . '/' .  $filename;
 		}
		/*if ($type == 'image'){
			return WindUrlHelper::checkUrl(PUBLIC_RES . '/images/medal/big', PUBLIC_URL);
		} else {
			return WindUrlHelper::checkUrl(PUBLIC_RES . '/images/medal/small', PUBLIC_URL);	
		}*/
	}
 	
 	private function _getMedalDs() {
		return Wekit::load('medal.PwMedalInfo');
	}
	
 	private function _getMedalLogDs() {
		return Wekit::load('medal.PwMedalLog');
	}
	
	private function _getMedalUserDs() {
		return Wekit::load('medal.PwMedalUser');
	}
	
}
?>