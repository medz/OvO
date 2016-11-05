<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖内置顶
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwPostsTopped {
	
	/**
	 * 获取某个帖子的所有置顶楼层
	 * 
	 * @param int $tid
	 * @return array
	 */
	public function getByTid($tid, $limit = 20, $offset = 0) {
		$tid = intval($tid);
		if ($tid < 1) return false;
		return $this->_getDao()->getByTid($tid, $limit, $offset);
	}
	
	/**
	 * 增加一个置顶楼层
	 * 
	 * @param PwPostsToppedDm $dm
	 * @return bool
	 */
	public function addTopped(PwPostsToppedDm $dm) {
		if (($result = $dm->beforeAdd()) instanceof PwError) return $result;
		return $this->_getDao()->add($dm->getData());
	}
	
	/**
	 * 删除某个置顶楼层
	 * 
	 * @param int $pid
	 * @return bool
	 */
	public function deleteTopped($pid) {
		$pid = intval($pid);
		if ($pid < 1) return false;
		return $this->_getDao()->delete($pid);
	}
	
	/**
	 * 删除某个置顶楼层
	 * 
	 * @param int $pid
	 * @return bool
	 */
	public function batchDeleteTopped($pids) {
		if (!is_array($pids) || !$pids) return false;
		return $this->_getDao()->batchDelete($pids);
	}

	/**
	 * 删除某个置顶楼层
	 * 
	 * @param int $pid
	 * @return bool
	 */
	public function updateTopped($pid, PwPostsToppedDm $dm) {
		$pid = intval($pid);
		if ($pid < 1) return false;
		if (($result = $dm->beforeUpdate()) instanceof PwError) return $result;
		return $this->_getDao()->update($pid, $dm->getData());
	}
	
	/**
	 * PwPostsToppedDao
	 *
	 * @return PwPostsToppedDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwPostsToppedDao');
	}
}