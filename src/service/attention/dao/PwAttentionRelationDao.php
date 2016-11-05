<?php
/**
 * 用户关注分类数据表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwAttentionRelationDao.php 19348 2012-10-13 03:52:48Z jinlong.panjl $
 * @package src.service.attention.dao
 */
class PwAttentionRelationDao extends PwBaseDao {

	protected $_table = 'attention_type_relations';
	protected $_dataStruct = array('uid', 'touid', 'typeid');

	public function getTypeByUidAndTouids($uid, $touids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? AND touid IN %s', $this->getTable(), $this->sqlImplode($touids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid));
	}

	public function count($uid) {
		$sql = $this->_bindTable('SELECT typeid, COUNT(*) AS count FROM %s WHERE uid=? GROUP BY typeid');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'typeid');
	}

	public function getUserByType($uid, $typeid, $limit, $offset) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid=? AND typeid=? %s', $this->getTable(), $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid, $typeid), 'touid');
	}
	
	public function batchAdd($uid, $touid, $typeids) {
		$data = array();
		foreach ($typeids as $key => $value) {
			$data[] = array('uid' => $uid, 'touid' => $touid, 'typeid' => intval($value));
		}
		$sql = $this->_bindSql('INSERT INTO %s (uid,touid,typeid) VALUES %s', $this->getTable(), $this->sqlMulti($data));
		$this->getConnection()->execute($sql);
		return true;
	}
	
	public function addUserType($uid, $touid, $typeid) {
		$fields = array('uid' => $uid, 'touid' => $touid, 'typeid' => intval($typeid));
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return true;
	}
	
	public function deleteByUidAndTouidAndType($uid, $touid, $typeid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND touid=? AND typeid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $touid, $typeid));
	}
	
	public function deleteByUidAndTouid($uid, $touid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE uid=? AND touid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($uid, $touid));
	}

	public function deleteByType($typeid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE typeid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($typeid));
	}
}