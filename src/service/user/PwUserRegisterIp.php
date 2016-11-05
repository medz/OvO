<?php

/**
 * 用户注册IP记录表数据服务接口
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserRegisterIp.php 5811 2012-03-12 10:36:04Z xiaoxia.xuxx $
 * @package src.service.user
 */
class PwUserRegisterIp {

	/** 
	 * 根据IP获取该条IP记录
	 *
	 * @param string $ip 查询的IP
	 * @return array
	 */
	public function getRecodeByIp($ip) {
		if (!$ip) return array();
		return $this->_getDao()->get($ip);
	}

	/** 
	 * 跟新最后记录
	 *
	 * @param string $ip ip地址
	 * @param int $lastDate 更新时间
	 * @return boolean
	 */
	public function updateRecodeByIp($ip, $lastDate) {
		if (!$ip) return false;
		!$lastDate && $lastDate = Pw::getTime();
		return $this->_getDao()->update($ip, $lastDate);
	}

	/** 
	 * 返回操作的Dao
	 *
	 * @return PwUserRegisterIpDao
	 */
	private function _getdao() {
		return Wekit::loadDao('user.dao.PwUserRegisterIpDao');
	}
}