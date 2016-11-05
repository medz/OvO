<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 13, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwNoticeThreadreply.php 3440 2012-01-17 08:25:36Z peihong.zhangph $
 */

Wind::import('SRV:message.srv.notice.PwNoticeAction');

class PwNoticeThreadreply extends PwNoticeAction{
	
	public $aggregate = true;
	public $ignoreNotice = true;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return '回复提醒';
	}
	
	/**
	 * 回复提醒相关扩展参数组装
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		if (!$aggregatedNotice || $aggregatedNotice['is_read']) {
			$extendParams['replyUser'] = array($extendParams['replyUserid']=>$extendParams['replyUsername']);
			return $extendParams;
		}
		
		$oldExtendParams = @unserialize($aggregatedNotice['extend_params']);
		
		//处理uids
		if (is_array($oldExtendParams['replyUser'])) {
			if (count($oldExtendParams['replyUser']) > 3) array_pop($oldExtendParams['replyUser']);
			
			if (false !== ($key = array_search($extendParams['replyUserid'], array_keys($oldExtendParams['replyUser'])))){
				unset($oldExtendParams['replyUser'][$key]);
			}
			$oldExtendParams['replyUser'][$extendParams['replyUserid']] = $extendParams['replyUsername'];
			$extendParams['replyUser'] = $oldExtendParams['replyUser'];
			$extendParams['pid'] = $oldExtendParams['pid'];
		}
		
		return $extendParams;
	}
	
	/**
	 * 
	 * 忽略一个回复通知
	 * @param array $notice
	 */
	public function ignoreNotice($notice,$ignore = 1){
		if (!$notice) {
			return false;
		}
		Wind::import('SRV:forum.dm.PwTopicDm');
		$dm = new PwTopicDm($notice['param']);
		$dm->setReplyNotice($ignore? 0 : 1);
		$this->_getThreadDs()->updateThread($dm);
	}
	
	/**
	 * 获取主题及最新回复
	 * @see PwNoticeAction::getDetailList()
	 */
	public function getDetailList($notice){
		$list = array();
		if (!$notice || !$notice['param']) {
			return $list;
		}
		$list['replyUsers'] = Wekit::load('user.PwUser')->fetchUserByUid($notice['extend_params']['uids'],PwUser::FETCH_MAIN);
		
/*		$list['thread'] = $this->_getThreadDs()->getThread($notice['param'],PwThread::FETCH_ALL);
		$list['newreplies'] = $this->_getThreadDs()->getPostByTid($notice['param'],0,20,false);
		$list['thread']['fid'] && $list['forum'] = $this->_getForumDs()->getForum($list['thread']['fid']);*/
		return $list;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwForum
	 */
	private function _getForumDs(){
		return Wekit::load('forum.PwForum');
	}
		
	/**
	 * 
	 * Enter description here ...
	 * @return PwThread
	 */
	private function _getThreadDs(){
		return Wekit::load('forum.PwThread');
	}
}