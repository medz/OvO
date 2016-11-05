<?php
/**
 * Enter description here ...
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwPatchDao.php 21505 2012-12-10 10:21:57Z long.shi $
 * @package wind
 */
class PwPatchDao extends PwBaseDao {
	protected $_table = 'patch';
	protected $_dataStruct = array('id', 'rule', 'name', 'status', 'time', 'description');

	/**
	 * 获取一个补丁
	 *
	 * @param unknown_type $id
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		return $this->_get($id);
	}
	
	/**
	 * 添加一个补丁
	 *
	 * @param unknown_type $id
	 * @param unknown_type $rule
	 * @param unknown_type $name
	 * @param unknown_type $status
	 * @param unknown_type $time
	 * @return Ambigous <boolean, number, string, rowCount>
	 */
	public function add($id, $rule, $name, $status, $time, $desc) {
		return $this->_add(
			array(
				'id' => $id, 
				'rule' => $rule, 
				'name' => $name, 
				'status' => $status, 
				'time' => $time,
				'description' => $desc), false);
	}

	/**
	 * 更新补丁状态
	 *
	 * @param unknown_type $id
	 * @param unknown_type $status
	 * @return Ambigous <boolean, number, rowCount>
	 */
	public function update($id, $status) {
		return $this->_update($id, array('status' => $status));
	}
	
	/**
	 * 更新旧补丁状态
	 *
	 * @param unknown_type $newest
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function updateOldPatch($newest) {
		$sql = $this->_bindSql('UPDATE %s SET status = 1 WHERE id <= ?');
		return $this->getConnection()->createStatement($sql)->execute(array($newest));
	}
	
	/**
	 * 删除一个补丁
	 *
	 * @param unknown_type $id
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function delete($id) {
		return $this->_delete($id);
	}
	
	/**
	 * 获取补丁列表
	 *
	 */
	public function getList() {
		$sql = $this->_bindTable('SELECT * FROM %s');
		return $this->getConnection()->query($sql)->fetchAll($this->_pk);
	}
	
	/**
	 * 获取最后的补丁
	 *
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function getMaxPatch() {
		$sql = $this->_bindTable('SELECT * FROM %s ORDER BY id DESC LIMIT 1');
		return $this->getConnection()->query($sql)->fetch();
	}
}

?>