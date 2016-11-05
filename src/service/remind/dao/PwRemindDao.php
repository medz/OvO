<?php

/**
 * @提醒DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwRemindDao extends PwBaseDao {
	
	protected $_pk = 'uid';
	protected $_table = 'remind';
	protected $_dataStruct = array('uid', 'touid');
	
	/**
	 * 查询一条
	 *
	 * @param int $uid
	 * @return bool
	 */
	public function get($uid) {
		return $this->_get($uid);
	}
	
	/**
	 * 添加
	 *
	 * @param array $data
	 * @return bool
	 */
	public function add($data) {
		return $this->_add($data);
	}
	
	/**
	 * 修改
	 *
	 * @param array $data
	 * @return bool
	 */
	public function replace($data) {
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 删除
	 *
	 * @param int $uid
	 * @return bool
	 */
	public function deleteByUid($uid) {
		return $this->_delete($uid);
	}

}