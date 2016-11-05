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
 * @version $Id: PwFreshReplyByPost.php 15448 2012-08-06 09:30:39Z jieyin $
 * @package src.service.user.srv
 */

class PwFreshReplyByPost {
	
	protected $post;
	protected $dm;
	protected $quote = '';

	public function __construct($fresh, PwUserBo $user) {
		if ($fresh['type'] == 2) {
			$post = $this->_getThread()->getPost($fresh['src_id']);
			$tid = $post['tid'];
			$rpid = $post['pid'];
			$post = $this->_getThread()->getPost($rpid);
			if ($post && $post['tid'] == $tid && $post['ischeck']) {
				$content = preg_replace('/\[quote(=.+?\,\d+)?\].*?\[\/quote\]/is', '', $post['content']);
				$this->quote = '[quote=' . $post['created_username'] . ',' . $rpid . ']' . Pw::substrs($content, 120) . '[/quote]';
			}
		} else {
			$tid = $fresh['src_id'];
			$rpid = 0;
		}
		$this->post = new PwPost(new PwReplyPost($tid, $user));
		$this->dm = $this->post->getDm();
		$this->dm->setReplyPid($rpid);
	}

	public function check() {
		return $this->post->check();
	}
	
	public function setContent($content) {
		$this->dm->setContent($this->quote . $content);
	}

	public function setIsTransmit($isTransmit) {
		if ($isTransmit) {
			Wind::import('HOOK:PwPost.do.PwPostDoFresh');
			$fresh = new PwPostDoFresh($this->post);
			$this->post->appendDo($fresh);
		}
	}

	public function execute() {
		return $this->post->execute($this->dm);
	}

	public function getIscheck() {
		return $this->dm->getField('ischeck');
	}
	
	public function getIsuseubb() {
		return $this->dm->getField('useubb');
	}

	public function getRemindUser() {
		return $this->dm->getField('reminds');
	}

	public function getNewFreshSrcId() {
		return $this->post->getNewId();
	}

	protected function _getThread() {
		return Wekit::load('forum.PwThread');
	}
}