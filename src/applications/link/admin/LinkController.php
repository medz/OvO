<?php
Wind::import('ADMIN:library.AdminBaseController');
/**
 * 添加友情链接
 *
 * @return void
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: LinkController.php 28814 2013-05-24 09:31:14Z jieyin $
 * @package controller.config
 */
class LinkController extends AdminBaseController {
	
	private $perpage = 20;

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$typeid = $this->getInput('typeid','get');
		//这里需要获取所有的链接列表，前台根据分类ID的筛选是js处理的
		$links = $this->_getLinkSrv()->getLinksList();
		$typesList = $this->_getLinkDs()->getAllTypes();
		
		$this->setTab('run');
		$this->setOutput($typeid, 'typeid');
		$this->setOutput($links, 'links');
		$this->setOutput($typesList, 'typesList');
	}
	
	/**
	 * dorun
	 *
	 * @return void
	 */
	public function dorunAction() {
		list($lid, $vieworder) = $this->getInput(array('lid', 'vieworder'), 'post');
		if (!$lid) $this->showError('operate.select');
		Wind::import('SRC:service.link.dm.PwLinkDm');
		foreach ($lid as $_id) {
			if (!isset($vieworder[$_id])) continue;
			$linkDm = new PwLinkDm($_id);
			$linkDm->setVieworder($vieworder[$_id]);
			$this->_getLinkDs()->updateLink($linkDm);
		}
		$this->showMessage('operate.success');
	}
	
	/**
	 * 添加友情链接
	 *
	 * @return void
	 */
	public function addAction() {
		$types = $this->_getLinkSrv()->getAllLinkTypes();
		$this->setOutput($types, 'types');
	}
	
	/**
	 * do添加友情链接
	 *
	 * @return void
	 */
	public function doaddAction() {
		list($vieworder,$name,$url,$descrip,$logo,$ifcheck,$contact,$typeids) = $this->getInput(array('vieworder','name','url','descrip','logo','ifcheck','contact','typeids'), 'post');
		if (!$typeids) {
			$this->showError('LINK:require_empty');
		}
		Wind::import('SRC:service.link.dm.PwLinkDm');
		$linkDm = new PwLinkDm();
		$linkDm->setVieworder($vieworder)
				->setName($name)
				->setUrl($url)
				->setDescrip($descrip)
				->setLogo($logo)
				->setIfcheck($ifcheck)
				->setContact($contact);
		$logo && $linkDm->setIflogo(1);
		if (($result = $this->_getLinkDs()->addLink($linkDm)) instanceof PwError) {
			$this->showError($result->getError());
		}
		foreach ($typeids as $v) {
			$this->_getLinkDs()->addRelation($result,$v);
		}
		
		$this->showMessage('ADMIN:success');
	}
	
	/**
	 * 编辑友情链接
	 *
	 * @return void
	 */
	public function editAction() {
		$types = $this->_getLinkSrv()->getAllLinkTypes();
		$lid = (int) $this->getInput('lid', 'get');
		$link = $this->_getLinkDs()->getLink($lid);
		$linkRelations = $this->_getLinkDs()->getRelationsByTypeId($lid);
		$typeIds = array();
		foreach ($linkRelations as $v) {
			$typeIds[] = $v['typeid'];
		}
		$this->setOutput($typeIds, 'typeIds');
		$this->setOutput($types, 'types');
		$this->setOutput($link, 'link');
	}
	
	/**
	 * do编辑友情链接
	 *
	 * @return void
	 */
	public function doeditAction() {
		list($vieworder,$name,$url,$descrip,$logo,$ifcheck,$contact,$typeids,$lid) = $this->getInput(array('vieworder','name','url','descrip','logo','ifcheck','contact','typeids','lid'), 'post');
		if (!$typeids) {
			$this->showError('LINK:require_empty');
		}
		Wind::import('SRC:service.link.dm.PwLinkDm');
		$linkDm = new PwLinkDm($lid);
		$linkDm->setVieworder($vieworder)
				->setName($name)
				->setUrl($url)
				->setDescrip($descrip)
				->setLogo($logo)	
				->setIfcheck($ifcheck)
				->setContact($contact);
		$logo && $linkDm->setIflogo(1);
		if (($result = $linkDm->beforeUpdate()) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->_getLinkDs()->updateLink($linkDm);
		$this->_getLinkDs()->delRelationsByLid($lid);
		foreach ($typeids as $v) {
			$this->_getLinkDs()->addRelation($lid,$v);
		}

		$this->showMessage('LINK:edit.success');
	}
	
	/**
	 * 删除友情链接
	 *
	 * @return void
	 */
	public function doDeleteAction() {
		$lid = $this->getInput('lid', 'post');
		if (!$lid) $this->showError('operate.select');
		if (($result = $this->_getLinkSrv()->batchDelete($lid)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage("operate.success");
	}
	
	/**
	 * 分类列表
	 *
	 * @return void
	 */
	public function typesAction() {
		$typesList = $this->_getLinkSrv()->getAllLinkTypes();
		$this->setTab('editTypes');
		$this->setOutput($typesList, 'typesList');
	}
	
	/**
	 * 编辑分类列表
	 *
	 * @return void
	 */
	public function dotypesAction() {
		list($data,$newdata) = $this->getInput(array('data','newdata'), 'post');

		is_array($data) || $data = array();
		foreach ($data as $k => $v) {
			if (!$v['typename']) continue;
			if (Pw::strlen($v['typename']) > 6) {
				$this->showError('Link:linkname.len.error');
			}
/*			$type = $this->_getLinkDs()->getTypeByName($v['typename']);
			if ($type && $type['typeid'] != $v['typeid']) {
				$this->showError('Link:type.exist');
			}
			*/
			$this->_getLinkDs()->updateLinkType($v['typeid'],$v['typename'],$v['vieworder']);
		}

		is_array($newdata) || $newdata = array();
		if ($newdata) {
			foreach ($newdata as $v) {
				if (!$v['typename']) continue;
				if (Pw::strlen($v['typename']) > 6) {
					$this->showError('Link:linkname.len.error');
				}
				$this->_getLinkDs()->addLinkType($v['typename'],$v['vieworder']);
			}
		}
		$this->showMessage("LINK:edit.success");
	}
	
	/**
	 * 添加分类
	 *
	 * @return void
	 */
	public function addTypeAction() {
	}
	
	/**
	 * do添加分类
	 *
	 * @return void
	 */
	public function doAddTypeAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($typename,$vieworder) = $this->getInput(array('typename','vieworder'), 'post');
		if (Pw::strlen($typename) > 6) {
			$this->showError('Link:linkname.len.error');
		}
		$type = $this->_getLinkDs()->getTypeByName($typename);
		if ($type) {
			$this->showError('Link:type.exist');
		}
		if (($result = $this->_getLinkDs()->addLinkType($typename, $vieworder)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage("ADMIN:success");
	}
	
	/**
	 * 删除分类
	 *
	 * @return void
	 */
	public function doDeleteTypeAction() {
		$typeId = (int)$this->getInput('typeId','post');
		if (!$typeId) {
			$this->showError('operate.fail');
		}

		if (($result = $this->_getLinkDs()->deleteType($typeId)) instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage("ADMIN:success");
	}
	
	/**
	 * 审核友情链接
	 *
	 * @return void
	 */
	public function checkAction() {
		list($page, $perpage) = $this->getInput(array('page', 'perpage'));
		$page = $page ? $page : 1;
		$perpage = $perpage ? $perpage : $this->perpage;
		list($start, $limit) = Pw::page2limit($page, $perpage);
		list($count, $links) = $this->_getLinkSrv()->getCheckLinksList($start, $limit, 0);
		if ($count) {
			$typesList = $this->_getLinkDs()->getAllTypes();
			$this->setOutput($typesList, 'typesList');
		}
		$this->setTab('check');
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($links, 'links');
	}
	
	/**
	 * do审核友情链接
	 *
	 * @return void
	 */
	public function doCheckAction() {
		$this->getRequest()->isPost() || $this->showError('operate.fail');

		list($data, $lid, $single) = $this->getInput(array('data', 'lid', 'signle'), 'post');
		if (!$lid) $this->showError('operate.select');
		Wind::import('SRC:service.link.dm.PwLinkDm');
		foreach ($lid as $_id) {
			if (!isset($data[$_id])) continue;
			$linkDm = new PwLinkDm($_id);
			$linkDm->setVieworder($data[$_id]['vieworder']);
			$linkDm->setIfcheck(1);
			$rt = $this->_getLinkDs()->updateLink($linkDm);
			if ($rt instanceof PwError) {
				$this->showError($rt->getError());
			}
			$this->_getLinkDs()->delRelationsByLid($_id);
			$typeids = $single ? explode(',', $data[$_id]['typeid']) : $data[$_id]['typeid'];
			foreach ($typeids as $v) {
				$this->_getLinkDs()->addRelation($_id, $v);
			}
		}
		$this->showMessage("operate.success");
	}
	
	/**
	 * 设置current
	 *
	 * @return void
	 */
	private function setTab($action) {
		$tabs = array('run' => '', 'editTypes' => '', 'check' => '');
		$tabs[$action] = 'current';
		$this->setOutput($tabs, 'tabs');
	}
	
	/**
	 * PwLinkService
	 *
	 * @return PwLinkService
	 */
	private function _getLinkSrv() {
		return Wekit::load('link.srv.PwLinkService');
	}
	
	/**
	 * PwLink
	 *
	 * @return PwLink
	 */
	private function _getLinkDs() {
		return Wekit::load('link.PwLink');
	}
}