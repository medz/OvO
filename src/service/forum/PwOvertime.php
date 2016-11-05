<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 各种帖子附加信息过期时间
 * 
 * @author peihong <jhqblxt@gmail.com> Mar 26, 2012
 * @link
 * @copyright
 * @license
 */

class PwOvertime {
	
	/**
	 * 设置某帖子过期时间
	 * 
	 * @param int $tid
	 * @param string $type
	 * @param int $overtime
	 */
	public function setOvertime($tid,$type,$overtime) {
		$tid = intval($tid);
		$overtime = intval($overtime);
		return $this->_getDao()->setOvertime($tid, $type, $overtime);
	}
	
	public function getOvertimeByTid($tid) {
		$tid = intval($tid);
		return $this->_getDao()->getOvertimeByTid($tid);
	}
	
	/**
	 * 获取帖子莫个类型的操作过期时间
	 *
	 * @param int $tid
	 * @param string $type
	 * @return array
	 */
	public function getOvertimeByTidAndType($tid, $type) {
		$tid = intval($tid);
		return $this->_getDao()->getOvertimeByTidAndType($tid, $type);
	}
	
	/**
	 * 设置多个帖子的操作过期时间
	 *
	 * @param array $tids
	 * @param string $type 类型
	 * @param int $overtime
	 * @return bool
	 */
	public function batchAdd($tids, $type, $overtime) {
		if (empty($tids) || !is_array($tids)) return false;
		$data = array();
		foreach ($tids as $tid) {
			$data[] = array(
				'tid' => $tid,
				'm_type' => $type,
				'overtime' => $overtime
			);
		}
		return $this->_getDao()->batchAdd($data);
	}
	
	/**
	 * 批量删除
	 *
	 * @param array $ids
	 * @return bool
	 */
	public function batchDelete($ids) {
		if (!$ids || !is_array($ids)) return false;
		return $this->_getDao()->batchDelete($ids);
	}
	
	/**
	 * 删除多个帖子的操作过期时间
	 *
	 * @param array $tids
	 * @param string $type 类型
	 * @return bool
	 */
	public function batchDeleteByTidAndType($tids, $type) {
		if (!$tids || !is_array($tids)) return false;
		return $this->_getDao()->batchDeleteByTidAndType($tids, $type);
	}
	
	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwOvertimeDao');
	}
}