<?php

/**
 * the last known user to change this file in the repository  <$LastChangedBy: xiaoxia.xuxx $>
 * @author $Author: xiaoxia.xuxx $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMedalFansDo.php 18820 2012-09-28 03:45:04Z xiaoxia.xuxx $ 
 * @package 
 */
class PwMedalFansDo {

	/**
	 * 添加关注
	 *
	 * @param int $uid
	 * @param int $touid
	 * @return boolean
	 */
	public function addFollow($uid, $touid) {
		Wind::import('SRV:medal.srv.PwAutoAwardMedal');
		$userBo = new PwUserBo($uid);
		$bp = new PwAutoAwardMedal($userBo);
		$bp->autoAwardMedal(8, isset($userBo->info['follows']) ? (int)$userBo->info['follows'] : 0);
		
		$userBo = new PwUserBo($touid);
		$bp = new PwAutoAwardMedal($userBo);
		$bp->autoAwardMedal(5, isset($userBo->info['fans']) ? (int)$userBo->info['fans'] : 0);
		return true;
	}

	/**
	 * 删除关注
	 *
	 * @param int $uid
	 * @param int $touid
	 * @return boolean
	 */
	public function delFollow($uid, $touid) {
		Wind::import('SRV:medal.srv.PwAutoRecoverMedal');
		
		$userBo = new PwUserBo($uid);
		$bp = new PwAutoRecoverMedal($userBo);
		$bp->autoRecoverMedal(8, isset($userBo->info['follows']) ? (int)$userBo->info['follows'] : 0);
		
		$userBo = new PwUserBo($touid);
		$bp = new PwAutoRecoverMedal($userBo);
		$bp->autoRecoverMedal(5, isset($userBo->info['fans']) ? (int)$userBo->info['fans'] : 0);
		return true;
	}
}
?>