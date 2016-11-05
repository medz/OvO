<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.post.PwReplyPost');
Wind::import('SRV:forum.srv.PwPost');

/**
 * 新鲜事回复
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshReplyListFromPost.php 14839 2012-07-27 03:09:00Z jieyin $
 * @package src.service.user.srv
 */

class PwFreshReplyListFromPost {
	
	protected $pid;

	public function __construct($fresh) {
		$this->pid = $fresh['src_id'];
	}

	public function getReplies($limit, $offset) {
		return $this->_getThread()->getPostByPid($this->pid, $limit, $offset, false);
	}

	protected function _getThread() {
		return Wekit::load('forum.PwPostsReply');
	}
}