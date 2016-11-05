<?php
! defined ( 'ACLOUD_PATH' ) && exit ( 'Forbidden' );
require_once Wind::getRealPath ( "ACLOUD_VER:customized.ACloudVerCustomizedFactory" );
class ACloudApiCustomizedMessage {
	
	public function countUnreadMessage($uid) {
		return $this->getVersionCustomizedMessage ()->countUnreadMessage ( $uid );
	}
	
	public function getMessageById($messageId) {
		return $this->getVersionCustomizedMessage ()->getMessageById ( $messageId );
	}
	
	public function getMessageByUid($uid, $offset, $limit) {
		return $this->getVersionCustomizedMessage ()->getMessageByUid ( $uid, $offset, $limit );
	}
	
	public function sendMessage($fromUid, $toUid, $content) {
		return $this->getVersionCustomizedMessage ()->sendMessage ( $fromUid, $toUid, $content );
	}
	
	public function replyMessage($messageid, $relationid, $uid, $content) {
		return $this->getVersionCustomizedMessage ()->replyMessage ( $messageid, $relationid, $uid, $content );
	}
	
	public function getMessageAndReply($dialogid,$offset, $limit) {
		return $this->getVersionCustomizedMessage ()->getMessageAndReply ( $dialogid, $offset, $limit );
	}
	
	public function getReplyThreadMessage($uid, $offset, $limit) {
		return $this->getVersionCustomizedMessage ()->getReplyThreadMessage ( $uid, $offset, $limit );
	}
	
	public function sendNotice($uid,$usernames,$messageInfo,$typeid){
		return $this->getVersionCustomizedMessage()->sendNotice($uid,$usernames,$messageInfo,$typeid);
	}

	public function sendFreshStat($uid,$content,$type){
		return $this->getVersionCustomizedMessage()->sendFreshStat($uid,$content,$type);
	}

	private function getVersionCustomizedMessage() {
		return ACloudVerCustomizedFactory::getInstance ()->getVersionCustomizedMessage ();
	}
}