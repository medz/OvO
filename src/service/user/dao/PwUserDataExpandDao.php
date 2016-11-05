<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户数据表data的扩展DAO
 * 该DAO不常用，用于后台积分设置的关联用户字段更新
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserDataExpandDao.php 14860 2012-07-27 06:03:44Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserDataExpandDao extends PwBaseDao {

	protected $_table = 'user_data';
	
	/**
	 * 获得数据表结构
	 *
	 * @return array
	 */
	public function getStruct() {
		$sql = $this->_bindTable('SHOW COLUMNS FROM %s');
		$tbFields = $this->getConnection()->createStatement($sql)->queryAll(array(), 'Field');
		return array_keys($tbFields);
	}

	/**
	 * 添加用户积分字段(>8以上的）
	 *
	 * @param int $num
	 * @return int
	 */
	public function alterAddCredit($num) {
		$sql = $this->_bindSql('ALTER TABLE %s ADD COLUMN credit%d INT(10) NOT NULL DEFAULT 0', $this->getTable(), $num);
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 删除用户积分字段（1-8不允许删除）
	 *
	 * @param int $num
	 * @return int
	 */
	public function alterDropCredit($num) {
		$sql = $this->_bindSql('ALTER TABLE %s DROP credit%d', $this->getTable(), $num);
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 清空用户的积分（只适用于1-8）
	 *
	 * @param int $num
	 * @return int
	 */
	public function clearCredit($num) {
		$sql = $this->_bindSql('UPDATE %s SET credit%d = 0 WHERE uid > 0', $this->getTable(), $num);
		return $this->getConnection()->execute($sql);
	}
}