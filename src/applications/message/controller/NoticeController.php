<?php

class NoticeController extends PwBaseController {

	public function beforeAction($handlerAdapter){
		parent::beforeAction($handlerAdapter);
		if (!$this->loginUser->isExists()) {
			$this->forwardRedirect(WindUrlHelper::createUrl('u/login/run'));
		}
		$action = $handlerAdapter->getAction();
		$controller = $handlerAdapter->getController();
		$this->setOutput($action,'_action');
		$this->setOutput($controller,'_controller');
	}

	public function run() {
		list($type,$page) = $this->getInput(array('type','page'));
		$page = intval($page);
		$page < 1 && $page = 1;
		$perpage = 20;
		list($start, $limit) = Pw::page2limit($page, $perpage);
		$noticeList = $this->_getNoticeDs()->getNotices($this->loginUser->uid,$type,$start, $limit);
		$noticeList = $this->_getNoticeService()->formatNoticeList($noticeList);
		$typeCounts = $this->_getNoticeService()->countNoticesByType($this->loginUser->uid);
		//类型
		$typeid = intval($type);
		//获取未读通知数
		$unreadCount = $this->_getNoticeDs()->getUnreadNoticeCount($this->loginUser->uid);

		$this->_readNoticeList($unreadCount,$noticeList);

		//count
		$count = intval($typeCounts[$typeid]['count']);
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput(ceil($count/$perpage), 'totalpage');
		$this->setOutput(array('type'=>$typeid),'args');
		$this->setOutput($typeid, 'typeid');
		$this->setOutput($typeCounts, 'typeCounts');
		$this->setOutput($noticeList, 'noticeList');

		// seo设置
		Wind::import('SRV:seo.bo.PwSeoBo');
		$seoBo = PwSeoBo::getInstance();
		$lang = Wind::getComponent('i18n');
		$seoBo->setCustomSeo($lang->getMessage('SEO:mess.notice.run.title'), '', '');
		Wekit::setV('seo', $seoBo);
	}

	/**
	 *
	 * 忽略消息
	 */
	public function ignoreAction(){
		list($id,$ignore) = $this->getInput(array('id','ignore'));
		if ($this->_getNoticeService()->ignoreNotice($id,$ignore)) {
			$this->showMessage('操作成功');
		} else {
			$this->showError('操作失败');
		}
	}

	/**
	 *
	 * 删除消息
	 */
	public function deleteAction(){
		list($id,$ids) = $this->getInput(array('id','ids'), 'post');
		if (!$ids && $id) $ids = array(intval($id));
        if(!is_array($ids))$this->showError('操作失败');
		if ($this->_getNoticeDs()->deleteNoticeByIdsAndUid($this->loginUser->uid, $ids)) {
			$this->showMessage('操作成功');
		} else {
			$this->showError('操作失败');
		}
	}

	/**
	 *
	 * 顶部快捷列表
	 */
	public function minilistAction(){
		$perpage = 20;
		$noticeList = $this->_getNoticeDs()->getNoticesOrderByRead($this->loginUser->uid, $perpage);
		$noticeList = $this->_getNoticeService()->formatNoticeList($noticeList);
		//获取未读通知数
		$unreadCount = $this->_getNoticeDs()->getUnreadNoticeCount($this->loginUser->uid);
		$this->_readNoticeList($unreadCount,$noticeList);
		//set layout for common request
		if (!$this->getRequest()->getIsAjaxRequest()){
			$this->setLayout('layout_notice_minilist');
		}
		$this->setOutput($noticeList, 'noticeList');
	}

	/**
	 *
	 * 具体通知详细页
	 */
	public function detaillistAction(){
		$id = $this->getInput('id');
		$notice = $this->_getNoticeDs()->getNotice($id);
		if (!$notice || $notice['uid'] != $this->loginUser->uid) {
			$this->showError('获取内容失败');
		}

		$detailList = $this->_getNoticeService()->getDetailList($notice);
		$this->setOutput($notice, 'notice');
		$this->setOutput($detailList,'detailList');
		$typeName = $this->_getNoticeService()->getTypenameByTypeid($notice['typeid']);
		$this->setOutput($typeName, 'typeName');
		//$tpl = $typeName ? sprintf('notice_detail_%s',$typeName) : 'notice_detail';
		//$this->setTemplate($tpl);
	}

	/**
	 *
	 * 具体通知详细页
	 */
	public function detailAction(){
		$id = $this->getInput('id');
		$notice = $this->_getNoticeDs()->getNotice($id);
		if (!$notice || $notice['uid'] != $this->loginUser->uid) {
			$this->showError('获取内容失败');
		}
		$prevNotice = $this->_getNoticeDs()->getPrevNotice($this->loginUser->uid,$id);
		$nextNotice = $this->_getNoticeDs()->getNextNotice($this->loginUser->uid,$id);
		$detailList = $this->_getNoticeService()->getDetailList($notice);
		$this->setOutput($notice, 'notice');
		$this->setOutput($detailList,'detailList');
		$this->setOutput($prevNotice, 'prevNotice');
		$this->setOutput($nextNotice, 'nextNotice');
		$typeName = $this->_getNoticeService()->getTypenameByTypeid($notice['typeid']);
		$this->setOutput($typeName, 'typeName');
		//$tpl = $typeName ? sprintf('notice_detail_%s',$typeName) : 'notice_detail';
		//$this->setTemplate($tpl);
	}

	/**
	 *
	 * Enter description here ...
	 * @return PwMessageNotices
	 */
	protected function _getNoticeDs(){
		return Wekit::load('message.PwMessageNotices');
	}

	/**
	 *
	 * Enter description here ...
	 * @return PwNoticeService
	 */
	protected function _getNoticeService(){
		return Wekit::load('message.srv.PwNoticeService');
	}

	/**
	 *
	 * Enter description here ...
	 * @return PwUser
	 */
	protected function _getUserDs(){
		return Wekit::load('user.PwUser');
	}

	/**
	 *
	 * 设置已读
	 * @param int $unreadCount
	 * @param array $noticeList
	 */
	private function _readNoticeList($unreadCount,$noticeList){
		if ($unreadCount && $noticeList) {
			//更新用户的通知未读数
			$readnum = 0; //本次阅读数
			Wind::import('SRV:message.dm.PwMessageNoticesDm');
			$dm = new PwMessageNoticesDm();
			$dm->setRead(1);
			$ids = array();
			foreach ($noticeList as $v) {
				if ($v['is_read']) continue;
				$readnum ++;
				$ids[] = $v['id'];
			}
			$ids && $this->_getNoticeDs()->batchUpdateNotice($ids,$dm);
			$newUnreadCount = $unreadCount - $readnum;
			if ($newUnreadCount != $unreadCount) {
				Wind::import('SRV:user.dm.PwUserInfoDm');
				$dm = new PwUserInfoDm($this->loginUser->uid);
				$dm->setNoticeCount($newUnreadCount);
				$this->_getUserDs()->editUser($dm,PwUser::FETCH_DATA);
			}
		}
	}
}