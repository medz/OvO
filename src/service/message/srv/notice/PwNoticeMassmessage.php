<?php
Wind::import('SRV:message.srv.notice.PwNoticeAction');
/**
 * 群发消息
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwNoticeMassmessage extends PwNoticeAction{
	
	public $aggregate = false;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return $extendParams['title'];
	}
	
	/**
	 * 
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		return $extendParams;
	}
	
	public function getDetailList($notice){
		
	}
}