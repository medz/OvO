<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:common.ACloudVerCommonFactory" );
class ACloudApiCommonPost {
	
	public function getPost($tid, $sort, $offset, $limit) {
		return $this->getVersionCommonPost ()->getPost ( $tid, $sort, $offset, $limit );
	}
	
	public function getPostByUid($uid, $offset, $limit) {
		return $this->getVersionCommonPost ()->getPostByUid ( $uid, $offset, $limit );
	}
	
	public function getPostByTidAndUid($tid, $uid, $offset, $limit) {
		return $this->getVersionCommonPost ()->getPostByTidAndUid ( $tid, $uid, $offset, $limit );
	}
	
	public function sendPost($tid, $uid, $title, $content) {
		return $this->getVersionCommonPost ()->sendPost ( $tid, $uid, $title, $content );
	}
	
	public function shieldPost($pid, $tid) {
		return $this->getVersionCommonPost ()->shieldPost ( $pid, $tid );
	}
	
	private function getVersionCommonPost() {
		return ACloudVerCommonFactory::getInstance ()->getVersionCommonPost ();
	}
}