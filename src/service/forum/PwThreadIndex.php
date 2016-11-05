<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadIndex.php 17055 2012-08-30 11:02:11Z jieyin $
 * @package forum
 */

class PwThreadIndex {
	
	/**
	 * 统计所有帖子数
	 *
	 * @return int
	 */
	public function count() {
		return $this->_getDao()->count();
	}
	
	/**
	 * 统计所有在版块序列中的帖子数
	 *
	 * @param array $fids 版块id序列
	 * @return int
	 */
	public function countThreadInFids($fids) {
		if (empty($fids) || !is_array($fids)) return 0;
		return $this->_getDao()->countThreadInFids($fids);
	}
	
	/**
	 * 统计所有不在版块序列中的帖子数
	 *
	 * @param array $fids 版块id序列
	 * @return int
	 */
	public function countThreadNotInFids($fids) {
		if (empty($fids) || !is_array($fids)) return $this->count();
		return $this->_getDao()->countThreadNotInFids($fids);
	}
	
	/**
	 * 获取帖子
	 *
	 * @param int $limit
	 * @param int $offset
	 * @param string $order 排序方式
	 * @return array
	 */
	public function fetch($limit, $offset, $order = 'lastpost') {
		$result = $this->_getDao()->fetch($limit, $offset, $order);
		return array_keys($result);
	}

	/**
	 * 获取在版块序列中的帖子
	 *
	 * @param array $fids 版块id序列
	 * @param int $limit
	 * @param int $offset
	 * @param string $order 排序方式
	 * @return array
	 */
	public function fetchInFid($fids, $limit, $offset, $order = 'lastpost') {
		if (empty($fids) || !is_array($fids)) return array();
		$result = $this->_getDao()->fetchInFid($fids, $limit, $offset, $order);
		return array_keys($result);
	}

	/**
	 * 获取不在版块序列中的帖子
	 *
	 * @param array $fids 版块id序列
	 * @param int $limit
	 * @param int $offset
	 * @param string $order 排序方式
	 * @return array
	 */
	public function fetchNotInFid($fids, $limit, $offset, $order = 'lastpost') {
		if (empty($fids) || !is_array($fids)) return $this->fetch($limit, $offset, $order);
		$result = $this->_getDao()->fetchNotInFid($fids, $limit, $offset, $order);
		return array_keys($result);
	}
	
	/**
	 * 删除多条数据
	 *
	 * @param int $limit
	 * @return bool
	 */
	public function deleteOver($limit) {
		if ($limit < 1) return false;
		return $this->_getDao()->deleteOver($limit);
	}

	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwThreadsIndexDao');
	}
}