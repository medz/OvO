<?php

/**
 * 前台登录错误日志 DS服务
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogLogin.php 21359 2012-12-05 08:01:23Z xiaoxia.xuxx $
 * @package src.service.log
 */
class PwLogLogin {
	const ERROR_PWD = 1;//密码错误
	const ERROR_SAFEQ = 2;//安全问题及答案错误

	/**
	 * 搜索日志
	 *
	 * @param PwLogSo $so
	 * @return array
	 */
	public function search(PwLogSo $so, $limit, $offset) {
		return $this->_getLogDao()->search($so->getCondition(), $limit, $offset);	
	}
	
	/**
	 * 根据条件统计日志
	 *
	 * @param PwLogSo $so
	 * @return int
	 */
	public function coutSearch(PwLogSo $so) {
		return $this->_getLogDao()->countSearch($so->getCondition());
	}
	
	/**
	 * 添加日志
	 *
	 * @param PwLogLoginDm $dm
	 * @return int
	 */
	public function addLog(PwLogLoginDm $dm) {
		if (true !== ($r = $dm->beforeAdd())) return $r;
		return $this->_getLogDao()->addLog($dm->getData());
	}
	
	/**
	 * 批量添加日志
	 *
	 * @param array $dms
	 * @return boolean
	 */
	public function batchAddLog($dms) {
		if (empty($dms)) return true;
		$datas = array();
		foreach ($dms as $_dm) {
			if (!$_dm instanceof PwLogLoginDm) {
				return false;
			}
			if (true !== ($r = $_dm->beforeAdd())) {
				return $r;
			}
			$data[] = $_dm->getData();
		}
		return $this->_getLogDao()->batchAddLog($data);
	}
	
	/**
	 * 根据日志ID删除日志
	 *
	 * @param int $id
	 * @return int
	 */
	public function deleteLog($id) {
		if (0 >= ($id = intval($id))) return false;
		return $this->_getLogDao()->deleteLog($id);
	}
	
	/**
	 * 根据日志ID列表批量删除日志
	 *
	 * @param array $logids
	 * @return int
	 */
	public function batchDeleteLog($logids) {
		if (empty($logids)) return false;
		return $this->_getLogDao()->batchDeleteLog($logids);
	}
	
	/**
	 * 清除某一个时间点之前的记录
	 *
	 * @param string $time
	 * @return boolean
	 */
	public function clearLogBeforeDatetime($time) {
		if (!$time) return false;
		return $this->_getLogDao()->clearLogBeforeDatetime($time);
	}
	
	/**
	 * @return PwLogLoginDao
	 */
	protected function _getLogDao() {
		return Wekit::loadDao('log.dao.PwLogLoginDao');
	}
}