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

class PwNoticeAttention extends PwNoticeAction{
	
	public $aggregate = true;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return '关注提醒';
	}
	
	/**
	 * 帖子管理通知相关扩展参数组装
	 * 
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		$oldExtendParams = array();
		if ($aggregatedNotice && !$aggregatedNotice['is_read']) {
			$oldExtendParams = $aggregatedNotice ? unserialize($aggregatedNotice['extend_params']) : $extendParams;
		}
		return array_slice($extendParams+$oldExtendParams, 0, 20,true);
	}
	
	public function getDetailList($notice){
		$list = $uids = array();
		$extendParams = $notice['extend_params'];

		$uids = array_keys($extendParams);
		$userList = Wekit::load('user.PwUser')->fetchUserByUid($uids, PwUser::FETCH_MAIN);
		$list['follows'] = $this->_getAttention()->fetchFollows($notice['uid'], array_keys($userList));
		$list['fans'] = WindUtility::mergeArray(array_unique($extendParams), $userList);
		return $list;
	}
	
	/**
	 * PwAttention
	 * 
	 * @return PwAttention
	 */
	private function _getAttention() {
		return Wekit::load('attention.PwAttention');
	}
}