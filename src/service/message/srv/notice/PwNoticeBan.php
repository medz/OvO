<?php
Wind::import('SRV:message.srv.notice.PwNoticeAction');
/**
 * 帐号管理--禁止/解禁消息扩展
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwNoticeBan.php 14409 2012-07-20 06:47:08Z xiaoxia.xuxx $
 * @package src.service.task.srv.notice
 */
class PwNoticeBan extends PwNoticeAction{
	
	public $aggregate = false;
	public $ignoreNotice = true;
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::buildTitle()
	 */
	public function buildTitle($param = 0, $extendParams = null, $aggregatedNotice = null) {
		switch($extendParams['ban']) {
			//禁止
			case 1:
				$title = sprintf('您的帐号已被%s执行%s操作。', $extendParams['operator'], implode('、', $extendParams['type']));
				break;
			//解禁:
			case 2:
				$title = sprintf('您的帐号已被%s解除%s限制。', $extendParams['operator'], implode('、', $extendParams['type']));
				break;
			//自动解禁
			case 3:
				$title = sprintf('您的帐号已自动解除%s限制。', implode('、', $extendParams['type']));
				break;
		}
		return $title;
	}
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams, $aggregatedNotice = null) {
		return $extendParams;
	}
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::getDetailList()
	 */
	public function getDetailList($notice) {
		return $notice;
	}
	
	/**
	 * 忽略
	 * 
	 * @param array $notice
	 */
	public function ignoreNotice($notice,$ignore = 1){
		if (!$notice) {
			return false;
		}
		return Wekit::load('message.srv.PwNoticeService')->setIgnoreNotice($notice['typeid'],$notice['uid'],$ignore);
	}
}