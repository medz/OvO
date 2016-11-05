<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 5, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwSpecialSortDao.php 3217 2011-12-14 06:42:36Z yishuo $
 */

class PwSpecialSortDao extends PwBaseDao {
	
	protected $_table = 'bbs_threads_sort';
	protected $_dataStruct = array('fid', 'tid', 'extra', 'sort_type', 'created_time', 'end_time');
	
	public function getSpecialSortByFid($fid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE fid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($fid), 'tid');
	}
	
	public function getSpecialSortByTid($tid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE tid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($tid), 'fid');
	}
	
	public function getSpecialSortByTypeExtra($sortType, $extra) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE sort_type=? AND extra=?', $this->getTable());
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($sortType, $extra), 'tid');
	}

	public function batchAdd($data) {
		$array = array();
		foreach ($data as $key => $value) {
			$array[] = array($value['fid'], $value['tid'], intval($value['extra']), $value['sort_type'], $value['created_time'], $value['end_time']);
		}
		$sql = $this->_bindSql('REPLACE INTO %s (fid, tid, extra, sort_type, created_time, end_time) VALUES %s', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}

	public function deleteSpecialSortByTid($tid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE tid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($tid));
	}

	public function batchDeleteSpecialSortByTid($tids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
		return $this->getConnection()->execute($sql);
	}
}