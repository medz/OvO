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

class PwNoticeDefault extends PwNoticeAction{
	
	public $aggregate = false;
	public $ignoreNotice = false;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		if ($extendParams['title']) {
			$title = $extendParams['title'];
		} else {
			$title = Pw::substrs($extendParams['content'], 60);
		}
		return $title;
	}
	
	/**
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		return $extendParams;
	}
	
	/**
	 * 
	 * 忽略一个回复通知
	 * @param array $notice
	 */
	public function ignoreNotice($notice,$ignore = 1){
	}
	
	/**
	 * @see PwNoticeAction::getDetailList()
	 */
	public function getDetailList($notice){
	}
}