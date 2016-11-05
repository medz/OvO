<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 根据用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchReplyByPid.php 7260 2012-03-31 08:24:12Z jieyin $
 * @package forum
 */
class PwFetchReplyByUid implements iPwDataSource {
	
	public $uid;

	public function __construct($uid) {
		$this->uid = $uid;
	}

	public function getData() {
		return Wekit::load('forum.PwThread')->getPostByUid($this->uid);
	}
}