<?php

/**
 * 帖子dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadsHitsDao.php 18768 2012-09-27 07:09:04Z jieyin $
 * @package forum
 */

class PwThreadsHitsDao extends PwBaseDao {
	
	protected $_table = 'bbs_threads_hits';
	protected $_thread_table = 'bbs_threads';
	protected $_pk = 'tid';
	protected $_dataStruct = array('tid', 'hits');
	
	public function get($tid) {
		return $this->_get($tid);
	}

	public function fetch($tids) {
		return $this->_fetch($tids, 'tid');
	}

	public function add($fields) {
		return $this->_add($fields, false);
	}

	public function update($tid, $hits) {
		$sql = $this->_bindTable('UPDATE %s SET hits=hits+? WHERE tid=?');
		$smt = $this->getConnection()->createStatement($sql);
		$result = $smt->update(array($hits, $tid), true);
		return $result === 0 ? $this->add(array('tid' => $tid, 'hits' => $hits)) : true;
	}

	public function syncHits() {
		$sql = $this->_bindSql('UPDATE %s a LEFT JOIN %s b ON a.tid=b.tid SET b.hits=b.hits+a.hits', $this->getTable(), $this->getTable($this->_thread_table));
		$this->getConnection()->execute($sql);
		$sql = $this->_bindTable('TRUNCATE TABLE %s');
		$this->getConnection()->execute($sql);
		return true;
	}
}