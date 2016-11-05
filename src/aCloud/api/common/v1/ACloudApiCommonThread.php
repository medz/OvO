<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:common.ACloudVerCommonFactory" );
class ACloudApiCommonThread {
	
	public function getByTid($tid) {
		return $this->getVersionCommonThread ()->getByTid ( $tid );
	}
	
	public function getByUid($uid, $offset = 0, $limit = 20) {
		return $this->getVersionCommonThread ()->getByUid ( $uid, $offset = 0, $limit = 20 );
	}
	
	public function getLatestThread($fids, $offset, $limit) {
		return $this->getVersionCommonThread ()->getLatestThread ( $fids, $offset, $limit );
	}
	
	public function getLatestThreadByFavoritesForum($uid, $offset, $limit) {
		return $this->getVersionCommonThread ()->getLatestThreadByFavoritesForum ( $uid, $offset, $limit );
	}
	
	public function getLatestThreadByFollowUser($uid, $offset, $limit) {
		return $this->getVersionCommonThread ()->getLatestThreadByFollowUser ( $uid, $offset, $limit );
	}
	
	public function getLatestImgThread($fids, $offset, $limit) {
		return $this->getVersionCommonThread ()->getLatestImgThread ( $fids, $offset, $limit );
	}
	
	public function getThreadImgs($tid) {
		return $this->getVersionCommonThread ()->getThreadImgs ( $tid );
	}
	
	public function getToppedThreadByFid($fid, $offset, $limit) {
		return $this->getVersionCommonThread ()->getToppedThreadByFid ( $fid, $offset, $limit );
	}
	
	public function getThreadByFid($fid, $offset, $limit) {
		return $this->getVersionCommonThread ()->getThreadByFid ( $fid, $offset, $limit );
	}
	
	public function getAtThreadByUid($uid, $offset, $limit) {
		return $this->getVersionCommonThread ()->getAtThreadByUid ( $uid, $offset, $limit );
	}
	
	public function getThreadByTopic($topic, $offset, $limit) {
		return $this->getVersionCommonThread ()->getThreadByTopic ( $topic, $offset, $limit );
	}
	
	public function postThread($uid, $fid, $subject, $content) {
		return $this->getVersionCommonThread ()->postThread ( $uid, $fid, $subject, $content );
	}
	
	public function shieldThread($tid, $fid) {
		return $this->getVersionCommonThread ()->shieldThread ( $tid, $fid );
	}
	
	private function getVersionCommonThread() {
		return ACloudVerCommonFactory::getInstance ()->getVersionCommonThread ();
	}
}