<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 根据版块/用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchTopicByFidAndUids.php 8617 2012-04-21 09:26:52Z jieyin $
 * @package forum
 */

class PwFetchTopicByFidAndUids implements iPwDataSource {
	
	public $fid;
	public $uids;

	public function __construct($fid, $uids) {
		$this->fid = $fid;
		$this->uids = $uids;
	}

	public function getData() {
		return Wekit::load('forum.PwThread')->getThreadsByFidAndUids($this->fid, $this->uids, 0, 0, PwThread::FETCH_ALL);
	}
}