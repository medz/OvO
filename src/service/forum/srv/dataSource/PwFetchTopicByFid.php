<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 根据版块/用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchTopicByFid.php 10467 2012-05-24 08:55:26Z jieyin $
 * @package forum
 */

class PwFetchTopicByFid implements iPwDataSource {
	
	public $fid;

	public function __construct($fid) {
		$this->fid = $fid;
	}

	public function getData() {
		return Wekit::load('forum.PwThread')->getThreadByFid($this->fid, 0, 0, PwThread::FETCH_ALL);
	}
}