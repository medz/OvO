<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.do.PwPostDoBase');

/**
 * 帖子发布-新鲜事 相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPostDoFresh.php 16258 2012-08-21 11:03:06Z jieyin $
 * @package forum
 */

class PwPostDoFresh extends PwPostDoBase {

	public $uid;
	public $postDm;
	public $forum;

	public function __construct(PwPost $pwpost) {
		$this->uid = $pwpost->user->uid;
		$this->forum = $pwpost->forum;
	}

	public function dataProcessing($postDm) {
		$this->postDm = $postDm;
		return $postDm;
	}

	public function addThread($tid) {
		if ($this->forum->isOpen() && $this->postDm->getIscheck()) {
			$fresh = $this->_getService();
			$fresh->send($this->uid, PwFresh::TYPE_THREAD_TOPIC, $tid);
		}
	}

	public function addPost($pid, $tid) {
		if ($this->forum->isOpen() && $this->postDm->getIscheck()) {
			$fresh = $this->_getService();
			$freshId = $fresh->send($this->uid, PwFresh::TYPE_THREAD_REPLY, $pid);
			Wekit::load('attention.PwFreshIndex')->add($freshId, $tid);
		}
	}

	protected function _getService() {
		return Wekit::load('attention.PwFresh');
	}
}