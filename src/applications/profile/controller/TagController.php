<?php
Wind::import('APPS:.profile.controller.BaseProfileController');
		
/**
 * 个性标签
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */
class TagController extends BaseProfileController {
	private $perpage = 16;
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$this->setCurrentLeft();
		$tags = $this->_getService()->getUserTagList($this->loginUser->uid);
		$num = $this->_getRelationDs()->countByUid($this->loginUser->uid);
		$hotTags = $this->_getDs()->getHotTag($this->perpage, 0);
		$count = $this->_getDs()->countHotTag();
		$totalPage = ceil($count/$this->perpage);
		
		$this->setOutput($totalPage, 'total');
		$this->setOutput((10 - $num), 'allowNum');
		$this->setOutput($tags, 'mytags');
		$this->setOutput($hotTags, 'hotTags');
	}
	
	/**
	 * 添加标签
	 */
	public function doAddAction() {
		$tag = $this->getInput('tagName', 'post');
		$result = $this->_getService()->addUserTagToUid($this->loginUser->uid, $tag, Pw::getTime());
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->setOutput(array('id' => $result, 'name' => $tag), 'data');
		$this->showMessage('USER:tag.add.success');
	}
	
	/**
	 * 添加用户标签
	 */
	public function doAddByidAction() {
		$tagid = $this->getInput('tagid');
		$result = $this->_getService()->addTagRelationWithTagid($this->loginUser->uid, $tagid, Pw::getTime());
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:tag.add.success');
	}
	
	/**
	 * 删除用户的标签
	 */
	public function doDeleteAction() {
		$tagid = $this->getInput('tagid', 'post');
		if (!$tagid) {
			$this->showError('operate.fail');
		}
		$result = $this->_getRelationDs()->deleteRelation($this->loginUser->uid, $tagid);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->showMessage('USER:tag.delete.success');
	}
	
	/**
	 * 获得热门标签
	 */
	public function hotAction() {
		$page = intval($this->getInput('start'));
		$page < 0 && $page = 0;
		$count = $this->_getDs()->countHotTag();
		$totalPage = ceil($count/$this->perpage);
		$page > $totalPage && $page = 1;
		list($start, $limit) = Pw::page2limit($page, $this->perpage);
		$hotTags = $this->_getDs()->getHotTag($this->perpage, $start);
		$list = array();
		foreach ($hotTags as $_item) {
			$list[] = array('tag_id' => $_item['tag_id'], 'name' => $_item['name']);
		}
		$data = array('list' => $list, 'page' => $page + 1);
		$this->setOutput($data, 'data');
		$this->showMessage('');
	}
	
	/**
	 * 标签的DS
	 *
	 * @return PwUserTag
	 */
	private function _getDs() {
		return Wekit::load('usertag.PwUserTag');
	}
	
	/**
	 * 获得DS
	 * 
	 * @return PwUserTagRelation
	 */
	private function _getRelationDs() {
		return Wekit::load('usertag.PwUserTagRelation');
	}
	
	/**
	 * 个人标签的服务
	 *
	 * @return PwUserTagService
	 */
	private function _getService() {
		return Wekit::load('usertag.srv.PwUserTagService');
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseController::setDefaultTemplateName()
	 */
	protected function setDefaultTemplateName($handlerAdapter) {
		$this->setTemplate('profile_tag');
	}
}