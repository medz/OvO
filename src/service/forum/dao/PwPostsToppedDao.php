<?php
/**
 * @author peihong <jhqblxt@gmail.com> Mar 26, 2012
 * @link
 * @license
 * @version $Id: PwOvertimeDao.php 6075 2012-03-16 05:54:18Z peihong.zhangph $
 */

class PwPostsToppedDao extends PwBaseDao {
	
	protected $_pk = 'pid';
	protected $_table = 'bbs_posts_topped';
	protected $_dataStruct = array('pid', 'tid', 'floor', 'created_userid', 'created_time');
	
	public function getByTid($tid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE tid=? ORDER BY `created_time` DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($tid),'pid');
	}
	
	public function add($fields){
		return $this->_add($fields);
	}
		 
	public function delete($pid) {
		return $this->_delete($pid);
	}
		 
	public function batchDelete($pids) {
		return $this->_batchDelete($pids);
	}
	
	public function update($pid,$fields) {
		return $this->_update($pid,$fields);
	}
}