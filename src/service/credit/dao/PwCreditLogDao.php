<?php

/**
 * 积分策略设置具体内容DAO
 *
 * @author JianMin Chen <sky_hold@163.com> 2011-12-19
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditLogDao.php 6512 2012-03-21 08:28:48Z jieyin $
 * @package src.service.credit.dao
 */
class PwCreditLogDao extends PwBaseDao {

	protected $_table = 'credit_log';
	protected $_dataStruct = array('id', 'ctype', 'affect', 'logtype', 'descrip', 'created_userid', 'created_username', 'created_time');
	
	public function countLogByUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) AS sum FROM %s WHERE created_userid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}

	public function getLogByUid($uid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'id');
	}

	public function countBySearch($field) {
		list($where, $arg) = $this->_getWhere($field);
		$sql = $this->_bindSql('SELECT COUNT(*) AS sum FROM %s WHERE %s', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue($arg);
	}

	public function searchLog($field, $limit, $offset) {
		list($where, $arg) = $this->_getWhere($field);
		$sql = $this->_bindSql('SELECT * FROM %s WHERE %s ORDER BY id DESC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($arg, 'id');
	}

	public function batchAdd($data) {
		$array = array();
		foreach ($data as $key => $value) {
			$array[] = array(
				$value['ctype'],
				$value['affect'],
				$value['logtype'],
				$value['descrip'],
				$value['created_userid'],
				$value['created_username'],
				$value['created_time']
			);
		}
		$sql = $this->_bindSql('INSERT INTO %s (ctype, affect, logtype, descrip, created_userid, created_username, created_time) VALUES %s', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}

	private function _getWhere($field) {
		$where = '1';
		$arg = array();
		foreach ($field as $key => $value) {
			switch ($key) {
				case 'ctype':
					$where .= ' AND ctype=?';
					$arg[] = $value;
					break;
				case 'created_userid':
					$where .= ' AND created_userid=?';
					$arg[] = $value;
					break;
				case 'created_time_start':
					$where .= ' AND created_time>?';
					$arg[] = $value;
					break;
				case 'created_time_end':
					$where .= ' AND created_time<?';
					$arg[] = $value;
					break;
				case 'award':
					$where .= ' AND affect' . ($value == 1 ? '>' : '<') . '0';
					break;
			}
		}
		return array($where, $arg);
	}
}