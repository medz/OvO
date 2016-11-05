<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户策略的積分設置DS
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditLog.php 9609 2012-05-08 07:57:23Z jieyin $
 * @package src.service.credit
 */
class PwCreditLog {
	
	/**
	 * 统计用户积分日志条数
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countLogByUid($uid) {
		if (empty($uid)) return 0;
		return $this->_getDao()->countLogByUid($uid);
	}

	/**
	 * 获取用户的积分日志
	 *
	 * @param int $uid
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getLogByUid($uid, $limit = 20, $offset = 0) {
		if (empty($uid)) return array();
		return $this->_getDao()->getLogByUid($uid, $limit, $offset);
	}

	/**
	 * 批量添加积分日志
	 *
	 * @param array $dmArr PwCreditLogDm dm数组
	 * @return bool
	 */
	public function batchAdd($dmArr) {
		if (empty($dmArr) || !is_array($dmArr)) return false;
		$data = array();
		foreach ($dmArr as $key => $dm) {
			if (!($dm instanceof PwCreditLogDm)) continue;
			if ($dm->beforeAdd() instanceof PwError) continue;
			$data[] = $dm->getData();
		}
		return $data ? $this->_getDao()->batchAdd($data) : false;
	}
	
	/**
	 * 统计搜索影响记录
	 *
	 * @param object $sc 搜索条件
	 * @return int
	 */
	public function countBySearch(PwCreditLogSc $sc) {
		return $this->_getDao()->countBySearch($sc->getData());
	}

	/**
	 * 搜索积分日志
	 *
	 * @param object $sc 搜索条件
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function searchLog(PwCreditLogSc $sc, $limit = 20, $offset = 0) {
		return $this->_getDao()->searchLog($sc->getData(), $limit, $offset);
	}
	
	/**
	 * 获取某用户的积分操作次数记录
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getOperate($uid) {
		return $this->_getOperateDao()->get($uid);
	}
	
	/**
	 * 批量更新用户积分操作次数
	 *
	 * @param array $data 
	 * @例：array(
	 *		array($uid1, 'post_topic', 2, 1336462272),
	 *		array($uid2, 'post_reply', 5, 1336462272)
	 *		...
	 * )
	 * @return bool
	 */
	public function batchAddOperate($data) {
		if (empty($data) || !is_array($data)) return false;
		return $this->_getOperateDao()->batchAdd($data);
	}

	/**
	 * 返回策略设置DAO
	 *
	 * @return PwCreditStrategyDao
	 */
	private function _getDao() {
		return Wekit::loadDao('credit.dao.PwCreditLogDao');
	}

	private function _getOperateDao() {
		return Wekit::loadDao('credit.dao.PwCreditOperateLogDao');
	}
}