<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:customized.ACloudVerCustomizedFactory" );
class ACloudApiCustomizedForum {
	
	public function getAllForum() {
		return $this->getVersionCustomizedForum ()->getAllForum ();
	}
	
	public function getForumByFid($fid) {
		return $this->getVersionCustomizedForum ()->getForumByFid ( $fid );
	}
	
	public function getChildForumByFid($fid) {
		return $this->getVersionCustomizedForum ()->getChildForumByFid ( $fid );
	}
	
	public function getTopicType($fid) {
		return $this->getVersionCustomizedForum ()->getTopicType ( $fid );
	}
	
	private function getVersionCustomizedForum() {
		return ACloudVerCustomizedFactory::getInstance ()->getVersionCustomizedForum ();
	}
}