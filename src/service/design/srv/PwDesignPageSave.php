<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignPageSave.php 20204 2012-10-24 09:14:08Z gao.wanggao $ 
 * @package 
 */
class PwDesignPageSave {
	
	/**
	 * Enter description here ...
	 * @param int $pageid
	 * @param int $uniqueid 
	 */
	public function getNewPageId($pageid, $uniqueid = 0, $isunique = false) {
		Wind::import('SRV:design.dm.PwDesignPageDm');
		$ds = $this->_getPageDs();
		$pageInfo = $ds->getPage($pageid);
		if (!$pageInfo) return new PwError('operate.fail');
		$dm = new PwDesignPageDm($pageid);
		if ($isunique) {
			list($pagename, $id) = $this->getUniquePage($pageInfo['page_router'], $uniqueid);
			$dm->setIsUnique($id)
				->setName($pagename);
		} elseif ($pageInfo['page_type'] != PwDesignPage::PORTAL) {
			$dm->setIsUnique(0);
			if ($pageInfo['is_unique']) {
				$ds->deleteNoUnique($pageInfo['page_router'],0);
			}
		}

		//设计模式解锁		
		$dm->setDesignLock(0, 0);
		$ds->updatePage($dm);
		return $pageid;
	}
	
	/**
	 * 用于segment中所在信息更新
	 * Enter description here ...
	 * @param array $segments
	 * @param ing $pageid
	 */
	public function updateSegment($segments, $pageid) {
		$ds = $this->_getSegmentDs();
		$srv = $this->_getCompileService();
		$srv->setPageid($pageid);
		foreach ($segments AS $key=>$struct) {
			$srv->appendSegment($key);
			$tpl = $srv->reduceStructure($struct);
			$tpl = $srv->replaceModule($tpl);
			//$info = $ds->getSegment($key, $pageid);
			$ds->replaceSegment($key, $pageid, $tpl, $struct);
		}
		$srv->setIsDesign(false);
		$srv->afterDesign();
		return true;
	}
	
	protected function getUniquePage($router, $uniqueid) {
		switch ($router) {
			case 'bbs/read/run':
				$thread = Wekit::load('forum.PwThread')->getThread($uniqueid);
				$thread && $forum = Wekit::load('forum.PwForum')->getForum($thread['fid']);
				$array = array('帖子阅读页-'.$this->_filterForumHtml($forum['name']), $thread['fid']);
				break;
			case 'bbs/thread/run':
				$forum = Wekit::load('forum.PwForum')->getForum($uniqueid);	
				$array = array('版块列表页-'.$this->_filterForumHtml($forum['name']), $uniqueid);
				break;
			case 'bbs/cate/run':
				$forum = Wekit::load('forum.PwForum')->getForum($uniqueid);	
				$array = array('论坛分类页-'.$this->_filterForumHtml($forum['name']), $uniqueid);
				break;
			case 'bbs/cate/digest':
				$forum = Wekit::load('forum.PwForum')->getForum($uniqueid);	
				$array = array('版块精华-'.$this->_filterForumHtml($forum['name']), $uniqueid);
				break;	
			default:
				$array = array($router, $uniqueid);
		}
		return $array;
	}
	
	/**
	 * 过滤版块名称html
	 * Enter description here ...
	 * @param string $forumname
	 */
	private function _filterForumHtml($forumname) {
		return  preg_replace('/<SPAN(.*)>(.*)<\/SPAN>/isU', '\\2', $forumname);
	}
	
	private function _getCompileService() {
		return Wekit::load('design.srv.PwDesignCompile');
	}
	
	private function _getSegmentDs() {
		return Wekit::load('design.PwDesignSegment');
	}
	
	private function _getPageDs() {
		return Wekit::load('design.PwDesignPage');
	}
	
	private function _getBakDs() {
		return Wekit::load('design.PwDesignBak');
	}
}
?>