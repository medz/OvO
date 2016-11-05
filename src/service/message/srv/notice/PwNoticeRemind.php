<?php
Wind::import('SRV:message.srv.notice.PwNoticeAction');
/**
 * @提醒
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwNoticeRemind extends PwNoticeAction{
	
	public $aggregate = false;
	public $ignoreNotice = false;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return Pw::substrs(Pw::stripWindCode($extendParams['content'],true), 40);
	}
	
	/**
	 * 
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		return $extendParams;
	}
	
	public function getDetailList($notice){
		return $notice;
	}
}