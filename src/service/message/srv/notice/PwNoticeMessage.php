<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 13, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwNoticeMessage.php 3440 2012-01-17 08:25:36Z peihong.zhangph $
 */

Wind::import('SRV:message.srv.notice.PwNoticeAction');

class PwNoticeMessage extends PwNoticeAction{
	
	public $aggregate = true;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return Pw::substrs(strip_tags($extendParams['content']), 28);
	}
	
	/**
	 * 帖子管理通知相关扩展参数组装
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		$fromUserInfo = $this->_getUserDs()->getUserByUid($extendParams['from_uid']);
		$extendParams['from_username'] = $fromUserInfo['username'];
		return $extendParams;
	}
	
	public function getDetailList($notice){
		$list = array();
		if (!$notice || !$notice['param']) {
			return $list;
		}
		$dialog = $this->_getMessagesService()->getDialogByUid($notice['uid'], $notice['param']);
		if (!$dialog) return $list;
		//$list = $this->_getMessagesDs()->getDialogMessages($notice['uid'], $notice['param'], 0, 20);
		$list = $this->_getWindid()->getMessageList($dialog['dialog_id'], 0, 20);
		krsort($list);
//		$list['newreplies'] = $this->_getThreadDs()->getPostByTid($notice['param'],0,20,false);
		$num = $this->_getWindid()->read($notice['uid'], $dialog['dialog_id'], array_keys($list));
		if ($num) {
			//$this->_getMessagesService()->resetDialogMessages($dialog['dialog_id']);
			$this->_getMessagesService()->resetUserMessages($dialog['to_uid']);
		}
		return array('data' => $list, 'dialog' => $dialog);
	}
	
	/**
	 * 
	 * @return PwMessageMessages
	 */
	private function _getWindid(){
		return WindidApi::api('message');
	}
	
	/**
	 * 
	 * @return PwMessageService
	 */
	private function _getMessagesService(){
		return Wekit::load('message.srv.PwMessageService');
	}
	
	/**
	 * 
	 * @return PwUser
	 */
	private function _getUserDs(){
		return Wekit::load('user.PwUser');
	}
}