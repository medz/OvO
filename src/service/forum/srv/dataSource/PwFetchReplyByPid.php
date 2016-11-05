<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 根据用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchReplyByPid.php 13224 2012-07-04 02:11:45Z jieyin $
 * @package forum
 */

class PwFetchReplyByPid implements iPwDataSource {
	
	public $pids;

	public function __construct($pids) {
		$this->pids = $pids;
	}

	public function getData() {
		return Wekit::load('forum.PwThread')->fetchPost($this->pids);
	}
}