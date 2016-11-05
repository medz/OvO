<?php
/**
 * 注册IP记录表数据接口
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserRegisterIpDao.php 22969 2013-01-04 06:03:43Z xiaoxia.xuxx $
 * @package service.user.dao
 */
class PwUserRegisterIpDao extends PwBaseDao {
	protected $_table = 'user_register_ip';
	protected $_pk = 'ip';
	protected $_dataStruct = array('ip', 'last_regdate', 'num');

	/** 
	 * 根据IP查询数据
	 *
	 * @param string $ip ip地址
	 * @return array
	 */
	public function get($ip) {
		return $this->_get($ip);
	}

	/** 
	 * 跟新某个IP的数据
	 *
	 * @param string $ip IP
	 * @param int $date 日期
	 * @return int
	 */
	public function update($ip, $date) {
		$data = array('ip' => $ip, 'last_regdate' => $date);
		$sql = $this->_bindSql('REPLACE INTO %s SET `num`=`num`+1,%s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
}