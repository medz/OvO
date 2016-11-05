<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:customized.ACloudVerCustomizedFactory" );
class ACloudApiCustomizedFriend {
	
	public function getAllFriend($uid, $offset, $limit) {
		return $this->getVersionCustomizedFriend ()->getAllFriend ( $uid, $offset, $limit );
	}
	
	public function searchAllFriend($uid, $keyword, $offset, $limit) {
		return $this->getVersionCustomizedFriend ()->searchAllFriend ( $uid, $keyword, $offset, $limit );
	}
	
	public function getFollowByUid($uid, $offset, $limit) {
		return $this->getVersionCustomizedFriend ()->getFollowByUid ( $uid, $offset, $limit );
	}
	
	public function addFollowByUid($uid, $uid2) {
		return $this->getVersionCustomizedFriend ()->addFollowByUid ( $uid, $uid2 );
	}
	
	public function deleteFollowByUid($uid, $uid2) {
		return $this->getVersionCustomizedFriend ()->deleteFollowByUid ( $uid, $uid2 );
	}
	
	public function getFanByUid($uid, $offset, $limit) {
		return $this->getVersionCustomizedFriend ()->getFanByUid ( $uid, $offset, $limit );
	}
	
	private function getVersionCustomizedFriend() {
		return ACloudVerCustomizedFactory::getInstance ()->getVersionCustomizedFriend ();
	}
}