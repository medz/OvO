<?php

/**
 * 关注完发通知
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwNoticeFansDo {

	public function addFollow($uid, $touid) {
		$userDs = Wekit::load('user.PwUser');
		$userInfo = $userDs->getUserByUid($uid, PwUser::FETCH_MAIN);
		// 加完关注发通知
		Wekit::load('message.srv.PwNoticeService')->sendNotice($touid,'attention',$touid,array($uid => array('uid' => $uid,'username' => $userInfo['username'])));
		return true;
	}
}
?>