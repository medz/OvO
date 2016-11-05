<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:usertag.so.PwUserTagSo');

/**
 * 用户个人标签
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class TagController extends AdminBaseController {
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		list($name, $ifhot, $min_count, $max_count, $page) = $this->getInput(array('name', 'ifhot', 'min_count', 'max_count', 'page'));
		$perpage = 10;
		$page = intval($page);
		$page < 1 && $page = 1;
		$tagSo = new PwUserTagSo();
		$tagSo->setName($name)->setIfhot($ifhot)->setMaxCount($max_count)->setMinCount($min_count);
		$total = $this->_getDs()->countSearchTag($tagSo);
		$totalPage = 0;
		$list = array();
		if ($total > 0) {
			$totalPage = ceil($total / $perpage);
			$page > $totalPage && $page = $totalPage;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			$list = $this->_getDs()->searchTag($tagSo, $limit, $start);
		}
		$this->setOutput($list, 'list');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($tagSo->getData(), 'args');
		$this->setOutput($page, 'page');
		$this->setOutput($total, 'count');
	}
	
	/**
	 * 删除标签
	 */
	public function deleteAction() {
		$ids = $this->getInput('ids', 'post');
		if (!$ids) $this->showError('USER:tag.ids.require');
		$result = $this->_getDs()->batchDeleteTag($ids);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:tag.delete.success', 'u/tag/run');
	}
	
	/**
	 * 设置为热门标签
	 */
	public function setHotAction() {
		$ids = $this->getInput('ids', 'post');
		if (!$ids) $this->showError('USER:tag.ids.require');
		$result = $this->_getDs()->batchUpdateTagHot($ids, 1);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:tag.sethot.success', 'u/tag/run');
	}
	
	/**
	 * 取消热门标签
	 */
	public function cancleHotAction() {
		$ids = $this->getInput('ids', 'post');
		if (!$ids) $this->showError('USER:tag.ids.require');
		$result = $this->_getDs()->batchUpdateTagHot($ids, 0);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:tag.sethot.success', 'u/tag/run');
	}
	
	/**
	 * 获得标签的DS服务
	 *
	 * @return PwUserTag
	 */
	private function _getDs() {
		return Wekit::load('usertag.PwUserTag');
	}
}