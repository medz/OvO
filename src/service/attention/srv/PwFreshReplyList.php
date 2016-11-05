<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 新鲜事回复
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshReplyList.php 13121 2012-07-02 03:47:00Z jieyin $
 * @package src.service.user.srv
 */

class PwFreshReplyList {
	
	public $fresh;
	public $id;
	public $bhv;
	protected $_bhv_map = array(
		1 => 'SRV:attention.srv.reply.PwFreshReplyListFromTopic',
		2 => 'SRV:attention.srv.reply.PwFreshReplyListFromPost',
		3 => 'SRV:attention.srv.reply.PwFreshReplyListFromWeibo'
	);

	public function __construct($fresh_id) {
		if ($fresh = $this->_getDs()->getFresh($fresh_id)) {
			$bhv = $this->_bhv_map[$fresh['type']];
			$class = Wind::import($bhv);
			$this->bhv = new $class($fresh);
		}
		$this->fresh = $fresh;
	}

	public function getReplies($limit = 10, $offset = 0) {
		if (!$this->bhv) return array();
		return $this->bhv->getReplies($limit, $offset);
	}

	public function getData() {
		return $this->fresh;
	}

	protected function _getDs() {
		return Wekit::load('attention.PwFresh');
	}
}