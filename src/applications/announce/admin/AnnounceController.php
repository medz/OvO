<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRC:service.announce.dm.PwAnnounceDm');
/**
 * 管理公告页
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: AnnounceController.php 28786 2013-05-23 09:57:58Z jieyin $
 * @package modules.admin
 */
class AnnounceController extends AdminBaseController {

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
       $announceInfo = array();
       $page = $this->getInput('page');
       $page < 1 && $page = 1;
       $perpage = 10;
       list($start, $limit) = Pw::page2limit($page, $perpage);
       $pageCount = $this->_getPwAnnounceDs()->countAnnounce();
       $announceInfos = $this->_getPwAnnounceDs()->getAnnounceOrderByVieworder($limit, $start);
       $announceInfos = $this->_getPwAnnounceService()->formatAnnouncesUsername($announceInfos);
       $this->setOutput($announceInfos, 'announceInfos');
       $this->setOutput($page, 'page');
       $this->setOutput($perpage, 'perpage');
       $this->setOutput($pageCount, 'pageCount');
	}
	
	/**
	 * 添加公告
	 *
	 * @return void
	 */
	public function addAction(){}


	/**

	 * 添加公告处理

	 *

	 * @return void

	 */
	
	public function doAddAction(){
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		$url = $this->getInput('url', 'post');
		$dm = new PwAnnounceDm();
		$url && $url = WindUrlHelper::checkUrl($url);
		$dm->setContent($this->getInput('content', 'post'))
			->setEndDate($this->getInput('end_date', 'post'))
			->setStartDate($this->getInput('start_date', 'post'))

			->setSubject($this->getInput('subject', 'post'))

			->setTypeid($this->getInput('typeid', 'post'))
			->setUrl($url)
			->setUid($this->loginUser->uid)
			->setVieworder($this->getInput('vieworder', 'post'));
		
		if (($result = $this->_getPwAnnounceDs()->addAnnounce($dm)) instanceof PwError){

			$this->showError($result->getError());

		}
		$this->showMessage('operate.success', 'announce/announce/run', true);
	}
	
	/**
	 * 公告列表页处理
	 *
	 * @return void
	 */
	public function doRunAction(){
		list($aid, $vieworders) = $this->getInput(array('aid', 'vieworder'), 'post');
		if(!$aid) $this->showError('operate.select');
        foreach($aid as $_id){
        	if (!isset($vieworders[$_id])) continue;
        	$dm = new PwAnnounceDm($_id);
        	$dm->setVieworder($vieworders[$_id]);
        	$this->_getPwAnnounceDs()->updateAnnounce($dm);
        }
		$this->showMessage('operate.success');
	}
	
	/**
	 * 通过多个公告ID批量删除多条公告
	 *
	 * @return void
	 */
	public function doBatchDeleteAction(){
		$aids = $this->getInput('aid', 'post');
		if (!$aids) $this->showError('operate.select');
		if (!$this->_getPwAnnounceDs()->batchDeleteAnnounce($aids))$this->showError('operate.fail');
		$this->showMessage('operate.success');
	}
	
	/**
	 * 通过单个公告ID删除单条公告
	 *
	 * @return void
	 */
	public function doDeleteAction(){
		$aid = $this->getInput('aid', 'post');
		if(!$aid || !$this->_getPwAnnounceDs()->deleteAnnounce($aid))$this->showError('operate.fail');
		$this->showMessage("operate.success");
	}
	
	/**
	 * 编辑公告处理
	 *
	 * @return void
	 */
	public function doUpdateAction(){
		list($aid, $url) = $this->getInput(array('aid', 'url'),'post');
		if ($aid < 1) $this->showError('operate.fail');

		$dm = new PwAnnounceDm($aid);
		$url && $url = WindUrlHelper::checkUrl($url);
		$dm->setContent($this->getInput('content', 'post'))
		   ->setEndDate($this->getInput('end_date', 'post'))
		   ->setStartDate($this->getInput('start_date', 'post'))
		   ->setSubject($this->getInput('subject', 'post'))
		   ->setTypeid($this->getInput('typeid', 'post'))
		   ->setUrl($url)
		   ->setUid($this->loginUser->uid)
		   ->setVieworder($this->getInput('vieworder', 'post'));
		if (($result = $this->_getPwAnnounceDs()->updateAnnounce($dm)) instanceof PwError){
			$this->showError($result->getError());
		}
		$this->showMessage('operate.success', 'announce/announce/run');
	}
	
	/**
	 * 编辑公告
	 *
	 * @return void
	 */
	public function updateAction(){
		$showType = array();
		$aid = $this->getInput('aid', 'get');
		if($aid < 1) $this->showError('ADMIN:fail');
		$announceInfo = $this->_getPwAnnounceDs()->getAnnounce($aid);
		$announceInfo['start_date'] && $announceInfo['start_date'] = Pw::time2str($announceInfo['start_date'], 'Y-m-d');
		$announceInfo['end_date'] && $announceInfo['end_date'] = Pw::time2str($announceInfo['end_date'], 'Y-m-d');
		$showType[$announceInfo['typeid']] = 'checked';
		$this->setOutput($announceInfo, 'announceInfo');
		$this->setOutput($showType, 'showType');
	}
	
	/**
	 * 加载PwAnnounceService Service 服务
	 *
	 * @return PwAnnounceService
	 */
	private function _getPwAnnounceService() {
		return Wekit::load('announce.srv.PwAnnounceService');
	}
	
	/**
	 * 加载PwAnnounce Ds 服务
	 *
	 * @return PwAnnounce
	 */
	private function _getPwAnnounceDs() {
		return Wekit::load('announce.PwAnnounce');
	}
}
