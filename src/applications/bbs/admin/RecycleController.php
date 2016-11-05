<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('ADMIN:library.AdminBaseController');

/**
 * 后台菜单管理操作类
 *
 * @author Qiong Wu <papa0924@gmail.com> 2011-10-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: RecycleController.php 28789 2013-05-23 10:11:36Z jieyin $
 * @package admin
 * @subpackage controller
 */

class RecycleController extends AdminBaseController {
	
	/**
	 * 菜单管理主入口
	 * 
	 * @return void
	 */
	public function run() {
		
		$page = intval($this->getInput('page'));
		list($keyword, $fid, $author, $createdTimeStart, $createdTimeEnd, $operator, $operateTimeStart, $operateTimeEnd) = $this->getInput(array('keyword', 'fid', 'author', 'created_time_start', 'created_time_end', 'operator', 'operate_time_start', 'operate_time_end'));
		
		$page < 1 && $page = 1;
		$perpage = 20;
		list($start, $limit) = Pw::page2limit($page, $perpage);

		Wind::import('SRV:recycle.vo.PwRecycleThreadSo');
		$so = new PwRecycleThreadSo();
		$so->orderbyCreatedTime(0);
		$url = array();
		
		if ($keyword) {
			$so->setKeywordOfTitle($keyword);
			$url['keyword'] = $keyword;
		}
		if ($author) {
			$so->setAuthor($author);
			$url['author'] = $author;
		}
		if ($fid) {
			$so->setFid($fid);
			$url['fid'] = $fid;
		}
		if ($createdTimeStart) {
			$so->setCreateTimeStart(Pw::str2time($createdTimeStart));
			$url['created_time_start'] = $createdTimeStart;
		}
		if ($createdTimeEnd) {
			$so->setCreateTimeEnd(Pw::str2time($createdTimeEnd));
			$url['created_time_end'] = $createdTimeEnd;
		}
		if ($operator) {
			$so->setOperator($operator);
			$url['operator'] = $operator;
		}
		if ($operateTimeStart) {
			$so->setOperatorTimeStart(Pw::str2time($operateTimeStart));
			$url['operate_time_start'] = $operateTimeStart;
		}
		if ($operateTimeEnd) {
			$so->setOperatorTimeEnd(Pw::str2time($operateTimeEnd));
			$url['operate_time_end'] = $operateTimeEnd;
		}

		$service = Wekit::load('recycle.PwTopicRecycle');
		$count = $service->countSearchRecord($so);
		$threaddb = $service->searchRecord($so, $limit, $start);

		$this->setOutput($threaddb, 'threaddb');
		$this->setOutput($this->_getFroumService()->getForumList($fid), 'forumList');
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');

		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput($url, 'url');
	}

	public function doDeleteTopicAction() {
		$tids = $this->getInput('tids', 'post');
		if (!$tids) $this->showError('operate.select');
		
		Wind::import('SRV:forum.srv.operation.PwDeleteTopic');
		Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
		$srv = new PwDeleteTopic(new PwFetchTopicByTid($tids), new PwUserBo($this->loginUser->uid));
		$srv->execute();

		$this->showMessage('删除成功了');
	}

	public function doRevertTopicAction() {
		$tids = $this->getInput('tids', 'post');
		if (!$tids) $this->showError('operate.select');

		Wind::import('SRV:forum.srv.operation.PwRevertTopic');
		$srv = new PwRevertTopic($tids, new PwUserBo($this->loginUser->uid));
		$srv->execute();

		$this->showMessage('还原成功了');
	}
	
	public function replyAction() {
		$threaddb = $params = array();
		list($keyword, $fid, $author, $createdTimeStart, $createdTimeEnd, $operator, $operateTimeStart, $operateTimeEnd) = $this->getInput(array('keyword', 'fid', 'author', 'created_time_start', 'created_time_end', 'operator', 'operate_time_start', 'operate_time_end'));
		$page = intval($this->getInput('page'));
		$page < 1 && $page = 1;
		$perpage = 20;
		list($start, $limit) = Pw::page2limit($page, $perpage);
		
		Wind::import('SRV:recycle.vo.PwRecycleReplySo');
		$so = new PwRecycleReplySo();
		$so->orderbyCreatedTime(0);
		$url = array();
		
		if ($keyword) {
			$so->setKeywordOfTitle($keyword);
			$url['keyword'] = $keyword;
		}
		if ($author) {
			$so->setAuthor($author);
			$url['author'] = $author;
		}
		if ($fid) {
			$so->setFid($fid);
			$url['fid'] = $fid;
		}
		if ($createdTimeStart) {
			$so->setCreateTimeStart(Pw::str2time($createdTimeStart));
			$url['created_time_start'] = $createdTimeStart;
		}
		if ($createdTimeEnd) {
			$so->setCreateTimeEnd(Pw::str2time($createdTimeEnd));
			$url['created_time_end'] = $createdTimeEnd;
		}
		if ($operator) {
			$so->setOperator($operator);
			$url['operator'] = $operator;
		}
		if ($operateTimeStart) {
			$so->setOperatorTimeStart(Pw::str2time($operateTimeStart));
			$url['operate_time_start'] = $operateTimeStart;
		}
		if ($operateTimeEnd) {
			$so->setOperatorTimeEnd(Pw::str2time($operateTimeEnd));
			$url['operate_time_end'] = $operateTimeEnd;
		}

		$service = Wekit::load('recycle.PwReplyRecycle');
		$count = $service->countSearchRecord($so);
		$threaddb = $service->searchRecord($so);
		
		$this->setOutput($this->_getFroumService()->getForumList($fid), 'forumList');
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setOutput($threaddb, 'threaddb');

		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput($count, 'count');
		$this->setOutput($url, 'url');
	}

	public function doDeleteReplyAction() {
		$pids = $this->getInput('pids', 'post');
		if (!$pids) $this->showError('operate.select');
		
		Wind::import('SRV:forum.srv.operation.PwDeleteReply');
		Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByPid');
		$srv = new PwDeleteReply(new PwFetchReplyByPid($pids), new PwUserBo($this->loginUser->uid));
		$srv->execute();

		$this->showMessage('删除成功了');
	}

	public function doRevertReplyAction() {
		$pids = $this->getInput('pids', 'post');
		if (!$pids) $this->showError('operate.select');

		Wind::import('SRV:forum.srv.operation.PwRevertReply');
		$srv = new PwRevertReply($pids, new PwUserBo($this->loginUser->uid));
		$srv->execute();
		$this->showMessage('还原成功了');
	}

	protected function _getFroumService() {
		return Wekit::load('forum.srv.PwForumService');
	}
}
?>