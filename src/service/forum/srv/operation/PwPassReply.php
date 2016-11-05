<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.PwGleanDoProcess');

/**
 * 回复通过审核及其关联操作(扩展)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPassReply.php 15534 2012-08-09 02:22:13Z jieyin $
 * @package forum
 */

class PwPassReply extends PwGleanDoProcess {
	
	public $data = array();
	public $pids = array();
	public $tids = array();
	public $fids = array();
	public $rpids = array();
	
	public function __construct(iPwDataSource $ds) {
		$this->data = $ds->getData();
		parent::__construct();
	}

	public function getData() {
		return $this->data;
	}

	protected function gleanData($value) {
		if ($value['disabled'] == 1) {
			$this->pids[] = $value['pid'];
			$this->tids[$value['tid']]++;
			$this->fids[$value['fid']]++;
			$value['rpid'] && $this->rpids[$value['rpid']]++;
		}
	}

	public function getIds() {
		return $this->pids;
	}

	protected function run() {
		Wind::import('SRV:forum.dm.PwReplyDm');
		Wind::import('SRV:forum.dm.PwTopicDm');
		$dm = new PwReplyDm(true);
		$dm->setDisabled(0);
		Wekit::load('forum.PwThread')->batchUpdatePost($this->pids, $dm);
		
		foreach ($this->tids as $tid => $value) {
			$post = current(Wekit::load('forum.PwThread')->getPostByTid($tid, 1, 0, false));
			$dm = new PwTopicDm($tid);
			$dm->addReplies($value);
			$dm->setLastpost($post['created_userid'], $post['created_username'], $post['created_time']);
			Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_MAIN);
		}
		foreach ($this->fids as $fid => $value) {
			Wekit::load('forum.srv.PwForumService')->updateStatistics($fid, 0, $value, $value);
		}
		foreach ($this->rpids as $rpid => $value) {
			$dm = new PwReplyDm($rpid);
			$dm->addReplies($value);
			Wekit::load('forum.PwThread')->updatePost($dm);
		}
		return true;
	}
}