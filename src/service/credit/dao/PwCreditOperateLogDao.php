<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 积分操作次数统计DAO
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditOperateLogDao.php 9609 2012-05-08 07:57:23Z jieyin $
 * @package src.service.credit.dao
 */
class PwCreditOperateLogDao extends PwBaseDao {

	protected $_table = 'credit_log_operate';
	protected $_dataStruct = array('uid', 'operate', 'num', 'update_time');
	
	public function get($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'operate');
	}

	public function batchAdd($data) {
		$array = array();
		foreach ($data as $key => $value) {
			$array[] = array(
				$value[0],
				$value[1],
				$value[2],
				$value[3]
			);
		}
		$sql = $this->_bindSql('REPLACE INTO %s (uid, operate, num, update_time) VALUES %s', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}
}