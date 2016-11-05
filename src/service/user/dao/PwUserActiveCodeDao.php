<?php

/**
 * 用户激活码记录表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserActiveCodeDao.php 10088 2012-05-16 12:18:26Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserActiveCodeDao extends PwBaseDao {
	protected $_table = 'user_active_code';
	protected $_dataStruct = array('uid', 'email', 'code', 'send_time', 'active_time', 'typeid');
	
	/** 
	 * 添加用户激活码
	 *
	 * @param array $data 激活码相关数据
	 * @return boolean|int
	 */
	public function insert($data) {
		if (!($data = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute();
	}
	
	/** 
	 * 更新用户激活码
	 *
	 * @param int $uid 用户ID
	 * @param int $activetime 激活时间
	 * @return boolean|int
	 */
	public function update($uid, $activetime) {
		$sql = $this->_bindTable('UPDATE %s SET `active_time`=? WHERE `uid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($activetime, $uid));
	}
	
	/** 
	 * 根据用户ID删除信息
	 *
	 * @param int $uid 用户ID
	 * @return int|boolean
	 */
	public function deleteByUid($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($uid));
	}
	
	/** 
	 * 根据用户ID获得信息
	 *
	 * @param int $uid 用户ID
	 * @param int $typeid 激活码类型
	 * @return array
	 */
	public function getInfoByUid($uid, $typeid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid`=? AND `typeid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($uid, $typeid));
	}
}