<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.dm.PwTopicDm');

/**
 * 单个帖子的业务模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadBo.php 22963 2013-01-04 05:17:05Z jieyin $
 * @package forum
 */

class PwThreadBo {

	public $tid;
	public $fid;
	public $author;
	public $authorid;

	public $info;

	public function __construct($tid) {
		$this->info = $this->_getThreadService()->getThread($tid, PwThread::FETCH_ALL);
		$this->tid  = $tid;
		$this->fid = $this->info['fid'];
		$this->author = $this->info['created_username'];
		$this->authorid = $this->info['created_userid'];
	}

	public function isThread() {
		return !empty($this->info);
	}

	public function isDeleted() {
		return $this->info['disabled'] == 2;
	}

	public function isChecked() {
		return $this->info['ischeck'] == 1;
	}

	public function getThreadInfo() {
		return $this->info;
	}

	public function getReplies($limit, $offset, $asc = true) {
		return $this->_getThreadService()->getPostByTid($this->tid, $limit, $offset, $asc);
	}

	public function hit() {
		$this->_getThreadService()->updateHits($this->tid, 1);
		//$this->info['hits']++;
		//$dm = new PwTopicDm($this->tid);
		//$dm->addHits(1);
		//$this->_getThreadService()->updateThread($dm);
	}

	public function appendHits() {
		if ($result = $this->_getThreadService()->getHit($this->tid)) {
			$this->info['hits'] += $result['hits'];
		}
	}

	protected function _getThreadService() {
		return Wekit::load('forum.PwThread');
	}
}
?>