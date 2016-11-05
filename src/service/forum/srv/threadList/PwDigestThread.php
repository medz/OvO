<?php
defined('WEKIT_VERSION') || exit('Forbidden');
Wind::import('SRV:forum.srv.threadList.PwThreadDataSource');

/**
 * 获取精华帖子列表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDigestThread.php 21318 2012-12-04 09:24:09Z jieyin $
 * @package src.service.forum.srv.threadList
 */
class PwDigestThread extends PwThreadDataSource {

	protected $fid;
	protected $type;
	protected $orderby;

	/**
	 * 构造方法
	 *
	 * @param PwForumBo $forum
	 * @param string $type
	 */
	public function __construct($fid, $type, $orderby) {
		$this->fid = $fid;
		$this->type = intval($type);
		$this->orderby = $orderby != 'postdate' ? 'lastpost' : 'postdate';
		$type && $this->urlArgs['type'] = $type;
		$this->urlArgs['tab'] = 'digest';
	}

	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getTotal()
	 */
	public function getTotal() {
		return $this->_getThreadDigestIndexDs()->countByFid($this->fid, $this->type);
	}

	/* (non-PHPdoc)
	 * @see PwThreadDataSource::getData()
	 */
	public function getData($limit, $offset) {
		$threads = $this->_getThreadDigestIndexDs()->getThreadsByFid($this->fid, $this->type, $limit, $offset, $this->orderby);
		$tids = array_keys($threads);
		$threaddb = $this->_getThreadDs()->fetchThread($tids);
		return $this->_sort($threaddb, $tids);
	}

	/**
	 * 帖子DS
	 *
	 * @return PwThread
	 */
	protected function _getThreadDs() {
		return Wekit::load('forum.PwThread');
	}

	/**
	 * 精华DS
	 *
	 * @return PwThreadDigestIndex
	 */
	protected function _getThreadDigestIndexDs() {
		return Wekit::load('forum.PwThreadDigestIndex');
	}

	/**
	 * 根据帖子ID$sort排序
	 *
	 * @param array $data
	 * @param array $sort
	 * @return array
	 */
	protected function _sort($data, $sort) {
		$result = array();
		foreach ($sort as $tid) {
			$result[$tid] = $data[$tid];
		}
		return $result;
	}
}