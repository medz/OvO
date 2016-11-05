<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:forum.srv.operation.PwDeleteTopic');
Wind::import('SRV:forum.srv.operation.PwDeleteReply');
Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
Wind::import('SRV:forum.srv.dataSource.PwFetchReplyByPid');

class ArticleController extends AdminBaseController {
	
	private $perpage = 20;

	public function run() {
		$fid = '';
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setTemplate('article_searchthread');
	}

	public function threadadvancedAction() {
		$fid = '';
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setTemplate('article_searchthread_advanced');
	}
	
	public function searchthreadAction() {
		list($page, $perpage, $keyword, $created_username, $time_start, $time_end, $fid, $digest, $created_userid, $created_ip, $hits_start, $hits_end, $replies_start, $replies_end) = $this->getInput(array('page', 'perpage', 'keyword', 'created_username', 'time_start', 'time_end', 'fid', 'digest', 'created_userid', 'created_ip', 'hits_start', 'hits_end', 'replies_start', 'replies_end'));
		if ($created_username) {
			$user = $this->_getUserDs()->getUserByName($created_username);
			if (!$user) $this->showError(array('USER:exists.not', array('{username}' => $created_username)));
			if ($created_userid) {
				($created_userid != $user['uid']) && $this->showError('USER:username.notequal.uid');
			}
			$created_userid = $user['uid'];
		}
		// dm条件
		Wind::import('SRV:forum.vo.PwThreadSo');
		$dm = new PwThreadSo();
		$keyword && $dm->setKeywordOfTitleOrContent($keyword);
		if ($fid) {
			$forum = Wekit::load('forum.PwForum')->getForum($fid);
			if ($forum['type'] != 'category') {
				$dm->setFid($fid);
			} else {
				$srv = Wekit::load('forum.srv.PwForumService');
				$fids = array(0);
				$forums = $srv->getForumsByLevel($fid, $srv->getForumMap());
				foreach ($forums as $value) {
					$fids[] = $value['fid'];
				}
				$dm->setFid($fids);
			}
		}
		$created_userid && $dm->setAuthorId($created_userid);
		$time_start && $dm->setCreateTimeStart(Pw::str2time($time_start));
		$time_end && $dm->setCreateTimeEnd(Pw::str2time($time_end));
		$digest && $dm->setDigest($digest);
		$hits_start && $dm->setHitsStart($hits_start);
		$hits_end && $dm->setHitsEnd($hits_end);
		$replies_start && $dm->setRepliesStart($replies_start);
		$replies_end && $dm->setRepliesEnd($replies_end);
		$created_ip && $dm->setCreatedIp($created_ip);
		$dm->setDisabled(0)->orderbyCreatedTime(false);
		$count = $this->_getThreadDs()->countSearchThread($dm);
		if ($count) {
			$page = $page ? $page : 1;
			$perpage = $perpage ? $perpage : $this->perpage;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			$threads = $this->_getThreadDs()->searchThread($dm,$limit,$start);
		}
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput(array(
			'keyword' => $keyword, 
			'created_username' => $created_username, 
			'time_start' => $time_start, 
			'time_end' => $time_end, 
			'fid' => $fid, 
			'digest' => $digest, 
			'created_userid' => $created_userid, 
			'created_ip' => $created_ip, 
			'hits_start' => $hits_start,
			'hits_end' => $hits_end, 
			'replies_start' => $replies_start, 
			'replies_end' => $replies_end,
		), 'args');
		
		$this->setOutput($this->_getFroumService()->getForumList($fid), 'forumList');
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setOutput($threads, 'threads');
	}
	
	public function removeAction() {
		
	}
	
	public function deletethreadAction() {
		$isDeductCredit = $this->getInput('isDeductCredit');
		$tids = $this->getInput('tids', 'post');
		if (!is_array($tids) || !count($tids)) {
			$this->showError('operate.select');
		}
		$service = new PwDeleteTopic(new PwFetchTopicByTid($tids), new PwUserBo($this->loginUser->uid));
		$service->setRecycle(true)->setIsDeductCredit((bool)$isDeductCredit)->execute();
				
		$this->showMessage('operate.success');
	}
	
	public function replylistAction() {
		$fid = '';
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setTemplate('article_searchreply');
	}
	
	public function replyadvancedAction() {
		$fid = '';
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setTemplate('article_searchreply_advanced');
	}
	
	public function searchreplyAction() {
		list($page, $perpage, $keyword, $fid, $created_username, $created_time_start, $created_time_end, $created_userid, $created_ip, $tid) = $this->getInput(array('page', 'perpage', 'keyword', 'fid', 'created_username', 'created_time_start', 'created_time_end', 'created_userid', 'created_ip', 'tid'));
		if ($created_username) {
			$user = $this->_getUserDs()->getUserByName($created_username);
			if (!$user) $this->showError('USER:username.empty');
			if ($created_userid) {
				($created_userid != $user['uid']) && $this->showError('USER:username.notequal.uid');
			}
			$created_userid = $user['uid'];
		}
		// dm条件
		Wind::import('SRV:forum.vo.PwPostSo');
		$dm = new PwPostSo();
		$dm->setDisabled(0)->orderbyCreatedTime(false);
		$keyword && $dm->setKeywordOfTitleOrContent($keyword);
		if ($fid) {
			$forum = Wekit::load('forum.PwForum')->getForum($fid);
			if ($forum['type'] != 'category') {
				$dm->setFid($fid);
			} else {
				$srv = Wekit::load('forum.srv.PwForumService');
				$fids = array(0);
				$forums = $srv->getForumsByLevel($fid, $srv->getForumMap());
				foreach ($forums as $value) {
					$fids[] = $value['fid'];
				}
				$dm->setFid($fids);
			}
		}
		$created_userid && $dm->setAuthorId($created_userid);
		$created_time_start && $dm->setCreateTimeStart(Pw::str2time($created_time_start));
		$created_time_end && $dm->setCreateTimeEnd(Pw::str2time($created_time_end));
		$tid && $dm->setTid($tid);
		$created_ip && $dm->setCreatedIp($created_ip);
		
		$count = $this->_getThreadDs()->countSearchPost($dm);
		if ($count) {
			$page = $page ? $page : 1;
			$perpage = $perpage ? $perpage : $this->perpage;
			list($start, $limit) = Pw::page2limit($page, $perpage);
			$posts = $this->_getThreadDs()->searchPost($dm,$limit,$start);
		}
		$this->setOutput($count, 'count');
		$this->setOutput($page, 'page');
		$this->setOutput($perpage, 'perpage');
		$this->setOutput(array(
			'keyword' => $keyword, 
			'created_username' => $created_username, 
			'created_time_start' => $created_time_start, 
			'created_time_end' => $created_time_end, 
			'fid' => $fid, 
			'created_userid' => $created_userid, 
			'created_ip' => $created_ip, 
			'tid' => $tid,
		), 'args');
		
		$this->setOutput($this->_getFroumService()->getForumList($fid), 'forumList');
		$this->setOutput($this->_getFroumService()->getForumOption($fid), 'option_html');
		$this->setOutput($posts, 'posts');
	}
	
	/**
	 * Enter description here ...
	 *
	 */
	public function deletereplyAction() {
		$isDeductCredit = $this->getInput('isDeductCredit');
		$pids = $this->getInput('pids', 'post');
		if (!is_array($pids) || !count($pids)) {
			$this->showError('operate.select');
		}
		$service = new PwDeleteReply(new PwFetchReplyByPid($pids), new PwUserBo($this->loginUser->uid));
		$service->setRecycle(true)->setIsDeductCredit((bool)$isDeductCredit)->execute();
		$this->showMessage('operate.success');
	}

	/**
	 * Enter description here ...
	 *
	 * @return PwThread
	 */
	private function _getThreadDs(){
		return Wekit::load('forum.PwThread');
	}
	
	private function _getUserDs(){
		return Wekit::load('user.PwUser');
	}
	
	protected function _getFroumService() {
		return Wekit::load('forum.srv.PwForumService');
	}
}
?>