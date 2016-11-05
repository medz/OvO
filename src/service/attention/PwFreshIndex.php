<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 新鲜事与帖子关系索引 
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwFreshIndex.php 16249 2012-08-21 09:01:24Z jieyin $
 * @package fresh
 */

class PwFreshIndex {

	/**
	 * 获取某帖子下所有关联的新鲜事
	 *
	 * @param int $tid 帖子id
	 * @return array
	 */
	public function getByTid($tid) {
		if (empty($tid)) return array();
		return $this->_getDao()->getByTid($tid);
	}

	/**
	 * 获取多个帖子下所有关联的新鲜事
	 *
	 * @param array $tid 帖子id序列
	 * @return array
	 */
	public function fetchByTid($tids) {
		if (empty($tids) || !is_array($tids)) return array();
		return $this->_getDao()->fetchByTid($tids);
	}
	
	/**
	 * 添加一条帖子与新鲜事的关联
	 *
	 * @param int $freshId 新鲜事id
	 * @param itn $tid 帖子id
	 * @return array
	 */
	public function add($freshId, $tid) {
		if (empty($freshId) || empty($tid)) return false;
		$fields = array(
			'fresh_id' => $freshId,
			'tid' => $tid
		);
		return $this->_getDao()->add($fields);
	}

	protected function _getDao() {
		return Wekit::loadDao('attention.dao.PwFreshIndexDao');
	}
}