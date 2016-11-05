<?php

/**
 * 用户登录IP记录
 * 同一个IP每天尝试登录的次数限制
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserLoginIpRecode.php 5811 2012-03-12 10:36:04Z xiaoxia.xuxx $
 * @package src.service.user
 */
class PwUserLoginIpRecode {
	
	/**
	 * 更新登录记录
	 *
	 * @param string $ip
	 * @param int $lastTime
	 * @param int $error_count
	 * @return boolean
	 */
	public function updateRecode($ip, $lastTime, $error_count) {
		if (!($ip = trim($ip))) return false;
		return $this->_getDao()->update(array('ip' => $ip, 'last_time' => $lastTime, 'error_count' => intval($error_count)));
	}
	
	/**
	 * 根据IP获得记录
	 *
	 * @param string $ip
	 * @return array
	 */
	public function getRecode($ip) {
		if (!($ip = trim($ip))) return array();
		return $this->_getDao()->get($ip);
	}
	
	/**
	 * 返回用户登录IP限制记录的DAO
	 *
	 * @return PwUserLoginIpRecodeDao
	 */
	private function _getDao() {
		return Wekit::loadDao('user.dao.PwUserLoginIpRecodeDao');
	}
}