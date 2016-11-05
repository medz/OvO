<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: jinlong.panjl $>
 * @author $Author: jinlong.panjl $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNoticeMedal.php 17946 2012-09-10 09:54:00Z jinlong.panjl $ 
 * @package 
 */
Wind::import('SRV:message.srv.notice.PwNoticeAction');

class PwNoticeMedal extends PwNoticeAction{
	
	public $aggregate = false;
	public $ignoreNotice = true;
	
	public function buildTitle($param = 0,$extendParams = null,$aggregatedNotice = null){
		return  '勋章提醒';
	}
	
	/**
	 * 
	 * @see PwNoticeAction::formatExtendParams()
	 */
	public function formatExtendParams($extendParams,$aggregatedNotice = null){
		//$preExtendParams = $aggregatedNotice ? unserialize($aggregatedNotice['extend_params']) : array();
		/*switch ($extendParams['type']) {
			case 1:
				$extendParams['notice'] = sprintf('恭喜您获得 %s勋章，请到“我的勋章”页面领取。勋章支持显示顺序调整。',
					$extendParams['name'],
					$extendParams['reason'] ? sprintf('操作原因：%s', $extendParams['reason']) : ''
				);
				break;
			case 2:
				$extendParams['notice'] = sprintf('恭喜您获得管理员直接颁发的 %s勋章，请到“我的勋章”页面领取。勋章支持显示顺序调整。',
					$extendParams['name'],
					$extendParams['reason'] ? sprintf('操作原因：%s', $extendParams['reason']) : ''
				);
				break;
			case 3:	
				$extendParams['notice'] = sprintf('恭喜您申请的 %s勋章已通过审核，请到“我的勋章”页面领取。勋章支持显示顺序调整。',
					$extendParams['name'],
					$extendParams['reason'] ? sprintf('操作原因：%s', $extendParams['reason']) : ''
				);
				break;
			case 4:
				$extendParams['notice'] = sprintf('对不起，您提交的 %s申请因不符合条件，未通过审核，如有问题请联系管理员。',
					$extendParams['name'],
					$extendParams['reason'] ? sprintf('操作原因：%s', $extendParams['reason']) : ''
				);
				break;
			case 5:
				$extendParams['notice'] = sprintf('对不起，您的 %s被收回。',
					$extendParams['name'],
					$extendParams['reason'] ? sprintf('收回原因：%s', $extendParams['reason']) : ''
				);
				break;
			case 6:
				$extendParams['notice'] = sprintf('恭喜您获得 %s勋章。请到“我的勋章”页面查看',
					$extendParams['name']
				);
				break;
		}*/
		//array_unshift($preExtendParams, $extendParams);
		return $extendParams;
	}
	
	public function getDetailList($notice){
		if(!is_array($notice)) return array();
		$logIds = $logInfo = $medalIds = array();
		$notice['medalInfo'] = Wekit::load('medal.PwMedalInfo')->getMedalInfo($notice['extend_params']['medelId']);
		$notice['medalInfo']['image'] = Wekit::load('medal.srv.PwMedalService')->getMedalImage($notice['medalInfo']['path'], $notice['medalInfo']['image']);
		$notice['medalInfo']['log_id'] = $notice['extend_params']['logid'];
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
		$this->_getNoticesDs()->batchUpdateNoticeByUidAndType($notice['uid'], $notice['typeid'], $dm);
		return Wekit::load('message.srv.PwNoticeService')->setIgnoreNotice($notice['typeid'],$notice['uid'],$ignore);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwMessageNotices
	 */
	private function _getNoticesDs(){
		return Wekit::load('message.PwMessageNotices');
	}
}