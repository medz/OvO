<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:common.ACloudVerCommonFactory" );
define ( 'POST_INVALID_PARAMS', 301 );
class ACloudApiCommonForum {
	
	public function getAllForum() {
		return $this->getVersionCommonForum ()->getAllForum ();
	}
	
	public function getForumByFid($fid) {
		return $this->getVersionCommonForum ()->getForumByFid ( $fid );
	}
	
	public function getChildForumByFid($fid) {
		return $this->getVersionCommonForum ()->getChildForumByFid ( $fid );
	}
	
	public function getForumOption($fids) {
		return $this->getVersionCommonForum ()->getForumOption ( $fids );
	}
	
	private function getVersionCommonForum() {
		return ACloudVerCommonFactory::getInstance ()->getVersionCommonForum ();
	}
}