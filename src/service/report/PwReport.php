<?php

/**
 * 举报DS
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwReport {
	
	/**
	 * 添加私信
	 *
	 * @param PwReportDm $dm
	 * @return bool
	 */
	public function addReport(PwReportDm $dm) {
		if (($result = $dm->beforeAdd()) !== true) {
			return $result;
		}
		return $this->_getDao()->add($dm->getData());
	}
	
	/**
	 * 单条更新
	 *
	 * @param PwReportDm $dm 
	 * @return bool
	 */
	public function updateReport(PwReportDm $dm){
		if (($result = $dm->beforeUpdate()) !== true) {
			return $result;
		}
		return $this->_getDao()->update($dm->id, $dm->getData());
	}
	
	/**
	 * 批量更新
	 *
	 * @param array $ids 
	 * @param PwReportDm $dm 
	 * @return bool
	 */
	public function batchUpdateReport($ids, PwReportDm $dm){
		if (!is_array($ids) || !count($ids)) {
			return false;
		}
		if (($result = $dm->beforeUpdate()) !== true) {
			return $result;
		}
		return $this->_getDao()->batchUpdate($ids, $dm->getData());
	}
	
	/**
	 * 单条删除
	 *
	 * @param int $id
	 * @return bool
	 */
	public function deleteReport($id){
		$id = intval($id);
		if ($id < 1) return false;
		return $this->_getDao()->delete($id);
	}
	
	/**
	 * 批量删除
	 *
	 * @param array $ids
	 * @return bool
	 */
	public function batchDeleteReport($ids){
		if (!is_array($ids) || !count($ids)) {
			return false;
		}
		return $this->_getDao()->batchDelete($ids);
	}
	
	/**
	 * 获取单条
	 *
	 * @param int $id
	 * @return bool
	 */
	public function getReport($id){
		$id = intval($id);
		if ($id < 1) return array();
		return $this->_getDao()->get($id);
	}
	
	/**
	 * 批量获取
	 *
	 * @param array $ids
	 * @return bool
	 */
	public function fetchReport($ids){
		if (!is_array($ids) || !count($ids)) return array();
		return $this->_getDao()->fetch($ids);
	}
	
	/**
	 * 根据举报来源和是否处理统计数量
	 *
	 * @param int $type
	 * @param int $ifcheck
	 * @return array
	 */
	public function countByType($ifcheck, $type = null){
		$type = intval($type);
		$ifcheck = intval($ifcheck);
		return $this->_getDao()->countByType($ifcheck, $type);
	}
	
	/**
	 * 根据举报来源和是否处理取列表
	 *
	 * @param int $type
	 * @param int $ifcheck
	 * @return array
	 */
	public function getListByType($ifcheck, $type = null, $limit, $start){
		$type = intval($type);
		$ifcheck = intval($ifcheck);
		return $this->_getDao()->getListByType($ifcheck, $type, $limit, $start);
	}
	
	/**
	 * 获取举报收件人
	 *
	 * @return array
	 */
	public function getNoticeReceiver() {
		$report = Wekit::C()->getValues('report');
		return (array)$report['noticeReceiver'];
	}
	
	/**
	 * @return PwReportDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('report.dao.PwReportDao');
	}
}