<?php
/**
 * 通知业务
 *
 * @author peihong <peihong.zhangph@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNoticeService.php 3833 2012-01-12 03:32:27Z peihong.zhangph $
 * @package src.service.message.srv
 */
class PwNoticeService {
	
	/**
	 * 发送通知
	 * @param int $uid
	 * @param string $type
	 * @param int $param
	 * @param array $extendParams
	 * @param $updateUnRead 是否更新未读数
	 */
	public function sendNotice($uid,$type,$param = 0,$extendParams = array(),$updateUnRead = true){
		$action = $this->_getAction($type);
		if (!$action) return new PwError('MESSAGE::notice.type.undefined');
		
		$typeId = $this->_getTypeId($type);
		// 看是否发通知
		if ($this->_checkPrivate($uid,$typeId) !== true) {
			return false;
		}
		//aggregated notice
		Wind::import('SRV:message.dm.PwMessageNoticesDm');
		$dm = new PwMessageNoticesDm();
		
		$action->aggregate && $notice = $this->_getNoticesDs()->getNoticeByUid($uid,$typeId,$param);
		$extendParams = $action->formatExtendParams($extendParams,$notice);
		$noticeTitle = $action->buildTitle($param,$extendParams,$notice);
		
		$dm->setToUid($uid)
			->setRead(0)
			->setType($typeId)
			->setParam($param)
			->setExtendParams($extendParams)
			->setTitle($noticeTitle);
		if (!$notice) {
			$noticeId = $this->_getNoticesDs()->addNotice($dm);
		} else {
			$dm->setId($notice['id']);
			$dm->setModifiedTime(Pw::getTime());
			$this->_getNoticesDs()->updateNotice($dm);
			$noticeId = $notice['id'];
		}
		
		//更新通知未读数
		if ($updateUnRead && (!$notice || $notice['is_read'])){
			Wind::import('SRV:user.dm.PwUserInfoDm');
			$dm = new PwUserInfoDm($uid);
			$dm->addNotice(1);
			$this->_getUserDs()->editUser($dm,PwUser::FETCH_DATA);
		}
		return true;
	}
	
	/**
	 * 
	 * 发送一般通知(无类型)
	 * @param int $uid
	 * @param string $content
	 * @param string $title
	 */
	public function sendDefaultNotice($uid,$content,$title = ''){
		$extendParams = array('content' => $content, 'title' => $title);
		return $this->sendNotice($uid, 'default', 0,$extendParams);
	}
	
	/**
	 * 
	 * 按类型统计
	 * @param unknown_type $uid
	 */
	public function countNoticesByType($uid){
		$list = $this->_getNoticesDs()->countNoticesByType($uid);
		$data = array();
		if (is_array($list)) {
			$typeNames = $this->_getTypeNames();
			$typeIds = array_flip($this->_getTypes());
			foreach ($list as $v) {
				$type = $typeIds[$v['typeid']];
				if (!$type) continue;
				$data[0]['count'] += $v['num'];
				$data[$v['typeid']] = array(
					'typename'	=> $typeNames[$type],
					'type'	=> $type,
					'count'		=> $v['num']
				);
			}
			$data[0] && $data[0]['typename'] = '全部';
		}
		return $data;
	}
	
	/**
	 * 
	 * (忽略|取消忽略)一个通知
	 */
	public function ignoreNotice($id,$ignore = 1){
		$id = intval($id);
		$ignore = intval($ignore);
		$ignore = $ignore ? 1 : 0;
		$notice = $this->_getNoticesDs()->getNotice($id);
		if (!$notice) {
			return false;
		} else {
			Wind::import('SRV:message.dm.PwMessageNoticesDm');
			$dm = new PwMessageNoticesDm($id);
			$dm->setIgnore($ignore);
			$this->_getNoticesDs()->updateNotice($dm);
			//ingore to app
			$noticeAction = $this->_getActionByTypeid($notice['typeid']);
			if ($noticeAction && $noticeAction->ignoreNotice) {
				$noticeAction->ignoreNotice($notice,$ignore);
			}
			return true;
		}
	}
	
	/**
	 * 
	 * 
	 * @param array $notice
	 */
	public function getDetailList($notice){
		if (!is_array($notice)) return null;
		$action = $this->_getActionByTypeid($notice['typeid']);
		if (!$action) return null;
		return $action->getDetailList($notice);
	}
	
	public function formatNoticeList($noticeList){
		$typeIds = array_flip($this->_getTypes());
		$messageFromUids = array();
		$uid = 0;
		foreach ($noticeList as $k=>$v) {
			$v['extend_params'] = @unserialize($v['extend_params']);
			$v['type'] = $typeIds[$v['typeid']];
			if ($v['type'] == 'message') {
				$uid = $v['extend_params']['to_uid'];
				$messageFromUids[$k] = $v['param'];
			}
			$noticeList[$k] = $v;
		}
		//取私信相关信息
		$messageInfos = $this->_getWindid()->getDialogByUsers($uid,$messageFromUids);
		if ($messageInfos) {
			foreach ($messageInfos as $v) {
				$noticeKey = array_search($v['from_uid'], $messageFromUids);
				$extend = array(
					'title' => $this->_parseUrl($v['last_message']['content']),
					'unread_count' => $v['unread_count'],
					'message_count' => $v['message_count'],
				);
				$noticeList[$noticeKey]['message_extend_params'] = $extend;
			}
		}
		return $noticeList;
	}
	
	/**
	 * 
	 * 根据类型ID获取类型名
	 * @param int $typeid
	 * @return string
	 */
	public function getTypenameByTypeid($typeid){
		$typeNames = $this->_getTypeNames();
		$typeIds = array_flip($this->_getTypes());
		return $typeIds[$typeid];
	}
	
	/**
	 * 根据类型删除通知
	 * 
	 * @param int $uid
	 * @param string $type
	 * @param int $param
	 * @param bool
	 */
	public function deleteNoticeByType($uid,$type,$param) {
		$typeId = $this->_getTypeId($type);
		return $this->_getNoticesDs()->deleteNoticeByType($uid,$typeId,$param);
	}

	/**
	 * 根据uid删除通知
	 * 
	 * @param int $uid
	 * @param bool
	 */
	public function deleteNoticeByUid($uid){
		$this->_getNoticesDs()->deleteNoticeByUid($uid);
		Wind::import('SRV:user.dm.PwUserInfoDm');
		$user = Wekit::load('user.PwUser');
		$dm = new PwUserInfoDm($uid);
		$dm->setNoticeCount(0);
		$user->editUser($dm, PwUser::FETCH_DATA);
	}
	
	/**
	 * 根据类型批量删除通知
	 * 
	 * @param int $uid
	 * @param string $type
	 * @param array $params
	 * @param bool
	 */
	public function detchDeleteNoticeByType($uid,$type,$params) {
		$typeId = $this->_getTypeId($type);
		return $this->_getNoticesDs()->betchDeleteNoticeByType($uid,$typeId,$params);
	}
	
	/**
	 * 根据类型ID设置忽略
	 * 
	 * @param int $typeId
	 * @param int $uid
	 * @return bool
	 */
	public function setIgnoreNotice($typeId,$uid,$ignore = 1){
		$config = $this->_getMessagesDs()->getMessageConfig($uid);
		$noticeValue = $config['notice_types'] ? unserialize($config['notice_types']) : array();
		$newArray = array($typeId=>$typeId);
		if ($ignore) {
			$noticeValue = $noticeValue+$newArray;
		} else {
			$noticeValue = array_diff_key($noticeValue,$newArray);
		}
		
		return $this->_getMessagesDs()->setMessageConfig($uid,$config['privacy'],serialize($noticeValue));
	}
	
	/**
	 * 获取通知设置忽略类型
	 * 
	 * @return array
	 */
	public function getNoticeTypeSet(){
		$privateType = $this->_getNoticePrivateType();
		$types = $this->_getTypeNames();
		$tmpTypes = array();
		foreach ($privateType as $k => $v) {
			if (in_array($k, array_keys($types))) {
				$tmpTypes[$v] = $types[$k];
			}
		}
		return $tmpTypes;
	}
	
	/**
	 * 某个类型是否被忽略
	 * 
	 * @param int $uid
	 * @param int $typeId
	 * @return array
	 */
	public function isIgnoreNoticeType($uid, $typeId){
		if (!in_array($typeId, $this->_getNoticePrivateType())) {
			return false;
		}
		$config = $this->_getMessagesDs()->getMessageConfig($uid);
		if (!$config['notice_types']) return false;
		$types = unserialize($config['notice_types']);
		return !in_array($typeId,$types) ? false : true;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $type
	 * @return PwNoticeAction
	 */
	protected function _getAction($type){
		if (!$type || !in_array($type,array_keys($this->_getTypes()))) return false;
		list($type) = explode('_',$type);
		$actionMethod = sprintf('_get%sAction',ucfirst($type));
		if (!method_exists($this, $actionMethod)) {
			$type = strtolower($type);
			$className = sprintf('PwNotice%s', ucfirst($type));
			$fliePath = 'SRV:message.srv.notice.'.$className;
			Wind::import($fliePath);
			return new $className();
		} else {
			return $this->$actionMethod();
		}
	}
	
	private function _parseUrl($message) {
		$searcharray = array(
			"/\[url=((https?|ftp|gopher|news|telnet|mms|rtsp|thunder|ed2k)?[^\[\s]+?)(\,(1)\/?)?\](.+?)\[\/url\]/eis",
			"/\[url\]((https?|ftp|gopher|news|telnet|mms|rtsp|thunder|ed2k)?[^\[\s]+?)\[\/url\]/eis"
		);
		preg_match("/\[url\]((https?|ftp|gopher|news|telnet|mms|rtsp|thunder|ed2k)?[^\[\s]+?)\[\/url\]/eis", $message, $match);
		return $match[1] ? $match[1] : $message;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param int $typeId
	 * @return PwNoticeAction
	 */
	protected function _getActionByTypeid($typeId){
		$typeId = intval($typeId);
		$types = array_flip($this->_getTypes());
		return $this->_getAction($types[$typeId]);
	}
	
	private function _getNoticePrivateType(){
		return array(
			'medal'	=> 4,
			'task' => 5,
			'credit' => 14,
		);
	}
	
	private function _getTypes(){
		return array(
			'message' => 1,
			'default' => 2,
			'threadmanage'	=> 3,
			'medal'	=> 4,
			'task' => 5,
			'massmessage' => 6,
			'report_thread' => 7,
			'report_post' => 8,
			'report_message' => 9,
			'threadreply' 	=> 10,
			'attention' 	=> 11,	
			'remind' 	=> 12,	
			'ban' => 13,
			'credit' => 14,
			'postreply' => 15,
			'report_photo' => 16,
			'app' => 99
		);
	}
	
	private function _getTypeNames(){
		return array(
			'default'		=> '通知',
			'message'		=> '私信',
			'threadreply'	=> '回复提醒',
			'threadmanage'	=> '管理提醒',
			'medal'	=> '勋章',
			'task' => '任务',
			'massmessage' => '群发消息',
			'report_thread' => '帖子举报',
			'report_post' => '回复举报',
			'report_message' => '私信举报',
			'attention' 	=> '关注',	
			'remind' 	=> '@提醒',	
			'ban' => '帐号管理',
			'credit' => '积分变动',
			'postreply' => '楼层回复',
			'report_photo' => '照片举报',
			'app' => '应用通知',
		);
	}
	
	/**
	 * 检查通知设置权限
	 * 
	 * @param int $uid
	 * @param int $type
	 * @return bool
	 */
	public function _checkPrivate($uid,$typeId) {
		$config = $this->_getMessagesDs()->getMessageConfig($uid);
		if (!$config['notice_types']) return true;
		$noticeValue = unserialize($config['notice_types']);
		$noticeType = array_intersect_key($noticeValue,$this->getNoticeTypeSet());
		if (in_array($typeId, array_keys($noticeType))) {
			return false;
		}
		return true;
	}
	
	private function _getTypeId($typeName){
		$types = $this->_getTypes();
		if (!is_array($types) || !isset($types[$typeName])) return 0;
		return $types[$typeName];
	}
	
	
	private function _getWindid() {
		return WindidApi::api('message');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwMessageNotices
	 */
	private function _getNoticesDs(){
		return Wekit::load('message.PwMessageNotices');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwMessageMessages
	 */
	private function _getMessagesDs(){
		return Wekit::load('message.PwMessageMessages');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwUser
	 */
	private function _getUserDs(){
		return Wekit::load('user.PwUser');
	}
}