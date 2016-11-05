<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadCateIndex.php 17054 2012-08-30 10:51:39Z jieyin $
 * @package forum
 */

class PwThreadCateIndex {
	
	/**
	 * 统计分类(A)下帖子数
	 *
	 * @param int $cid 分类(A)
	 * @return int
	 */
	public function count($cid) {
		if (empty($cid)) return 0;
		return $this->_getDao()->count($cid);
	}
	
	/**
	 * 统计分类(A)下且不在版块序列(B)中的帖子数
	 *
	 * @param int $cid 分类(A)
	 * @param array fids 版块序列(B)
	 * @return int
	 */
	public function countNotInFids($cid, $fids) {
		if (empty($fids) || !is_array($fids)) return $this->count($cid);
		return $this->_getDao()->countNotInFids($cid, $fids);
	}
	
	/**
	 * 获取分类(A)的帖子
	 *
	 * @param int $cid 分类(A)
	 * @param int $limit
	 * @param int $offset
	 * @param string $order 排序方式
	 * @return array
	 */
	public function fetch($cid, $limit = 20, $offset = 0, $order = 'lastpost') {
		if (empty($cid)) return array();
		$result = $this->_getDao()->fetch($cid, $limit, $offset, $order);
		return array_keys($result);
	}

	/**
	 * 获取分类(A)下且不在版块序列(B)中的帖子
	 *
	 * @param int $cid 分类(A)
	 * @param array fids 版块序列(B)
	 * @param int $limit
	 * @param int $offset
	 * @param string $order 排序方式
	 * @return array
	 */
	public function fetchNotInFid($cid, $fids, $limit, $offset, $order = 'lastpost') {
		if (empty($fids) || !is_array($fids)) return $this->fetch($cid, $limit, $offset, $order);
		$result = $this->_getDao()->fetchNotInFid($cid, $fids, $limit, $offset, $order);
		return array_keys($result);
	}
	
	/**
	 * 删除分类(A)下多条数据
	 *
	 * @param int $cid 分类(A)
	 * @param int $limit
	 * @return bool
	 */
	public function deleteOver($cid, $limit) {
		if (empty($cid) || $limit < 1) return false;
		return $this->_getDao()->deleteOver($cid, $limit);
	}

	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwThreadsCateIndexDao');
	}
}