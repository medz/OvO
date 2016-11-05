<?php
!defined('ACLOUD_PATH') && exit('Forbidden');

define('PERMISSIONS_INVALID_PARAMS', 701);
define('PERMISSIONS_USER_NOT_EXISTS', 701);
class ACloudVerCommonPermissions extends ACloudVerCommonBase {

	/**
	 * Enter description here ...
	 *
	 * @param unknown_type $uid
	 * @return Ambigous <multitype:unknown , multitype:unknown NULL >
	 */
	public function isUserBanned($uid) {
		$user = new PwUserBo($uid);
		if (!$user->isExists()) return $this->buildResponse(PERMISSIONS_USER_NOT_EXISTS);
		// $result = $this->getUserBanService ()->getBanInfoByUid ( $uid, 1 );
		Wind::import('SRV:user.srv.PwBanBp');
		$banBp = new PwBanBp($uid);
		$result = $banBp->checkIfBanSpeak();
		if ($result instanceof PwError) return $this->buildResponse(-1, $result->getError());
		return $this->buildResponse($result ? 500 : 0);
	}

	/**
	 * 判断指定用户是否有访问指定版块的权限
	 *
	 * @param int $uid        	
	 * @param int $fid        	
	 */
	public function readForum($uid, $fid) {
		Wind::import('SRV:forum.bo.PwForumBo');
		$forum = new PwForumBo($fid);
		$user = new PwUserBo($uid);
		$result = $forum->allowVisit($user);
		if ($result instanceof PwError) return $this->buildResponse(-1, $result->getError());
		return $this->buildResponse($result ? 500 : 0);
	}
}