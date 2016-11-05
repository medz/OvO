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
 * @version $Id: PwFreshReplyListFromWeibo.php 20581 2012-10-31 08:46:10Z jieyin $
 * @package src.service.user.srv
 */

class PwFreshReplyListFromWeibo {
	
	protected $weibo_id;

	public function __construct($fresh) {
		$this->weibo_id = $fresh['src_id'];
	}

	public function getReplies($limit, $offset) {
		$replies = $this->_getDs()->getComment($this->weibo_id, $limit, $offset, false);
		foreach ($replies as $key => $value) {
			if (strpos($value['content'], '[s:') !== false) {
				$value['useubb'] = 1;
			}
			$replies[$key] = $value;
		}
		return $replies;
	}

	protected function _getDs() {
		return Wekit::load('weibo.PwWeibo');
	}
}