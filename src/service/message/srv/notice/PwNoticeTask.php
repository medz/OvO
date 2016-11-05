<?php
Wind::import('SRV:message.srv.notice.PwNoticeAction');
/**
 * 任务消息扩展
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.service.task.srv.notice
 */
class PwNoticeTask extends PwNoticeAction{
	
	public $aggregate = false;
	public $ignoreNotice = true;
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::buildTitle()
	 */
	public function buildTitle($param = 0, $extendParams = null, $aggregatedNotice = null) {
		return $extendParams['complete'] ? '任务奖励领取提醒' : '未完成任务提醒';
	}
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams, $aggregatedNotice = null) {
		$extendParams['condition'] = is_array($extendParams['conditions']) ? $extendParams['conditions'] : unserialize($extendParams['conditions']);
		$url = $extendParams['condition']['url'] ? $extendParams['condition']['url'] : 'task/index/run';
		$array = array('id' =>$extendParams['taskid'] ,'title' => $extendParams['title'], 'icon' => $extendParams['icon'], 'url' => $url, 'created_time' => Pw::getTime());
		$array['complete'] = isset($extendParams['complete']) && $extendParams['complete'] == 1 ? 1 : 0;
		return $array;
	}
	
	/* (non-PHPdoc)
	 * @see PwNoticeAction::getDetailList()
	 */
	public function getDetailList($notice) {
		$notice['is_ignore'] = Wekit::load('message.srv.PwNoticeService')->isIgnoreNoticeType($notice['uid'], $notice['typeid']);
		
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
		Wind::import('SRV:message.dm.PwMessageNoticesDm');
		$dm = new PwMessageNoticesDm();
		$dm->setIgnore($ignore);
		Wekit::load('message.PwMessageNotices')->batchUpdateNoticeByUidAndType($notice['uid'], $notice['typeid'], $dm);
		return Wekit::load('message.srv.PwNoticeService')->setIgnoreNotice($notice['typeid'],$notice['uid'],$ignore);
	}
}