<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 13, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwNoticeThreadmanage.php 3440 2012-01-17 08:25:36Z peihong.zhangph $
 */

Wind::import('SRV:message.srv.notice.PwNoticeAction');

class PwNoticeThreadmanage extends PwNoticeAction{
	
	public $aggregate = false;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return Pw::substrs($extendParams['content'], 80);
	}
	
	/**
	 * 帖子管理通知相关扩展参数组装
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		return $extendParams;
	}
	
	public function getDetailList($notice){
		
	}
}