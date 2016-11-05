<?php
/**
 * @author peihong <jhqblxt@gmail.com> Mar 26, 2012
 * @link
 * @license
 * @version $Id: PwOvertimeDao.php 6075 2012-03-16 05:54:18Z peihong.zhangph $
 */

class PwOvertimeDao extends PwBaseDao {
	
	protected $_table = 'bbs_threads_overtime';
	protected $_dataStruct = array('id', 'tid', 'm_type', 'overtime');
	
	public function setOvertime($tid,$type,$overtime){
		$fields = array(
			'tid' => $tid,
			'm_type' => $type,
			'overtime' => $overtime
		);
		$sql = $this->_bindSql('REPLACE INTO %s SET %s',$this->getTable(), $this->sqlSingle($fields));
		return $this->getConnection()->execute($sql);
	}
	
	public function getOvertimeByTid($tid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE tid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($tid));
	}
	
	public function getOvertimeByTidAndType($tid, $type) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE tid=? AND m_type=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($tid, $type));
	}

	public function batchAdd($data) {
		$array = array();
		foreach ($data as $value) {
			$array[] = array($value['tid'], $value['m_type'], $value['overtime']);
		}
		$sql = $this->_bindSql('REPLACE INTO %s (tid, m_type, overtime) VALUES %s', $this->getTable(), $this->sqlMulti($array));
		return $this->getConnection()->execute($sql);
	}
		
	public function deleteByTidAndType($tid, $type) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE tid=? AND m_type=?');
		return $this->getConnection()->createStatement($sql)->update(array($tid,$type));
	}
	
	public function batchDelete($ids) {
		return $this->_batchDelete($ids);
	}
	
	public function batchDeleteByTidAndType($tids, $type) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE tid IN %s AND m_type=?', $this->getTable(), $this->sqlImplode($tids));
		return $this->getConnection()->createStatement($sql)->update(array($type));
	}
}