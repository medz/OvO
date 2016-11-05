<?php

/**
 * 用户登录-密码尝试次数：IP尝试次数
 * 
 * 当天统一个IP登录，能尝试的密码次数记录表
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserLoginIpRecodeDao.php 5811 2012-03-12 10:36:04Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserLoginIpRecodeDao extends PwBaseDao {
	protected $_table = 'user_login_ip_recode';
	protected $_pk = 'ip';
	protected $_dataStruct = array('ip', 'last_time', 'error_count');
	
	/**
	 * 添加一次记录
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function update($data) {
		if (!($data = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 获得记录
	 *
	 * @param string $ip 登录Ip
	 * @return array
	 */
	public function get($ip) {
		return $this->_get($ip);
	}
}