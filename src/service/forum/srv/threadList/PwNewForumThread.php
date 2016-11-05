<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadList.PwThreadDataSource');

/**
 * 帖子列表数据接口 / 普通列表最新排序
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwNewForumThread.php 22495 2012-12-25 03:35:33Z jieyin $
 * @package forum
 */

class PwNewForumThread extends PwThreadDataSource {
	
	protected $forum;
	protected $specialSortTids;
	protected $count;
	protected $so;

	public function __construct($forum) {
		$this->forum = $forum;
		$this->specialSortTids = array_keys($this->_getSpecialSortDs()->getSpecialSortByFid($forum->fid));
		$this->count = count($this->specialSortTids);
		
		Wind::import('SRV:forum.vo.PwThreadSo');
		$this->so = new PwThreadSo();
		$this->so->setFid($forum->fid)->setDisabled(0)->orderbyCreatedTime(0);
	}

	public function getTotal() {
		return $this->forum->foruminfo['threads'] + $this->count;
	}

	public function getData($limit, $offset) {
		$threaddb = array();
		if ($offset < $this->count) {
			$array = $this->_getThreadDs()->fetchThreadByTid($this->specialSortTids, $limit, $offset);
			foreach ($array as $key => $value) {
				$value['issort'] = true;
				$threaddb[] = $value;
			}
			$limit -= count($threaddb);
		}
		$offset -= min($this->count, $offset);
		if ($limit > 0) {
			$array = $this->_getThreadDs()->searchThread($this->so, $limit, $offset);
			$array && $threaddb = array_merge($threaddb, $array);
		}
		return $threaddb;
	}

	protected function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}
	
	protected function _getSpecialSortDs() {
		return Wekit::load('forum.PwSpecialSort');
	}
}