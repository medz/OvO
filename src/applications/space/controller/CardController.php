<?php

/**
 * 小名片
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: CardController.php 19721 2012-10-17 07:42:35Z gao.wanggao $
 * @package src.products.user.controller
 */
class CardController extends PwBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {

		list($uid, $username) = $this->getInput(array('uid', 'username'));
		if ($uid) {

		} elseif ($username) {
			$info = Wekit::load('user.PwUser')->getUserByName($username);
			$uid = $info['uid'];
		} else {
			$uid = 0;
		}
		$user = new PwUserBo($uid, true);

		$medals = array();
		if ($uid) {
			/* @var $service PwAttention */
			$service = Wekit::load('attention.PwAttention');
			$isFollowed = $service->isFollowed($this->loginUser->uid, $uid);
			$isFans = $service->isFollowed($uid, $this->loginUser->uid);
			$follow2num = ($isFollowed || $uid == $this->loginUser->uid) ? 0 : $service->countFollowToFollow($this->loginUser->uid, $uid);
			if ($follow2num > 0) {
				$uids = $service->getFollowToFollow($this->loginUser->uid, $uid, 2);
				$usernames = Wekit::load('user.PwUser')->fetchUserByUid(array_keys($uids));
				$this->setOutput($usernames, 'usernames');
			}
			if (Wekit::C('site','medal.isopen')) {
				$medalIds = explode(',', $user->info['medal_ids']);
				$medals = Wekit::load('medal.srv.PwMedalCache')->fetchMedal($medalIds);
			}
		} else {
			$isFollowed = false;
			$isFans = false;
			$follow2num = 0;
			$user->info['follows'] = 0;
			$user->info['fans'] = 0;
			$user->info['postnum'] = 0;
		}
		
		$this->setOutput($user->info['gender'] == 1, 'female');
		$this->setOutput(Pw::checkOnline($user->info['lastvisit']), 'isol');
		$this->setOutput($uid, 'uid');
		$this->setOutput($follow2num, 'follow2num');
		$this->setOutput($isFollowed, 'isFollowed');
		$this->setOutput($isFans, 'isFans');
		$this->setOutput($user, 'user');
		$this->setOutput($medals, 'medals');
		$this->setOutput(count($medals), 'medalNum');
		$this->setTemplate('TPL:common.card_run');
	}
}