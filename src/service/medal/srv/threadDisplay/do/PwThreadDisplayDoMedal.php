<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');
Wind::import('SRV:medal.bo.PwUserMedalBo');

/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadDisplayDoMedal.php 19721 2012-10-17 07:42:35Z gao.wanggao $ 
 * @package 
 */
class PwThreadDisplayDoMedal extends PwThreadDisplayDoBase {

	public $medals;

	public function bulidUsers($users) {
		$medals = array();
		foreach ($users as $key => $value) {
			$value['medal_ids'] && $medals[$key] = $value['medal_ids'];
		}
		$this->medals = Wekit::load('medal.srv.PwMedalCache')->fetchUserMedal($medals);
		return $users;
	}

	public function createHtmlAfterUserInfo($user, $read) {
		if (isset($this->medals[$user['uid']])) {
			PwHook::template('displayMedalHtmlAfterContent', 'TPL:medal.read_medal', true, $this->medals[$user['uid']]);
		}
	}
}
?>