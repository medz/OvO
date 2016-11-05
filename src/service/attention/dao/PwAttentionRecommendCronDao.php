<?php

/**
 * 可能认识的人计划任务DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwAttentionRecommendCronDao extends PwBaseDao {

	protected $_pk = 'uid';
	protected $_table = 'attention_recommend_cron';
	protected $_dataStruct = array('uid', 'created_time');

	public function get($uid){
		$this->_get($uid);
	}
	
	public function getAll(){
		$sql = $this->_bindTable('SELECT * FROM %s');
		$rst = $this->getConnection()->query($sql);
		return $rst->fetchAll('uid');
	}
	
	public function replace($fields) {
		if (!$fields = $this->_filterStruct($fields)) {
			return false;
		}
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
		$this->getConnection()->execute($sql);
		return true;
	}
	
	public function delete($uid){
		return $this->_delete($uid);
	}
	
	public function deleteByCreatedTime($created_time){
		$sql = $this->_bindSql('DELETE FROM %s WHERE `created_time`<?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->update(array($created_time));
	}
		
	public function update($uid, $fields){
		return $this->_update($uid, $fields);
	}
}