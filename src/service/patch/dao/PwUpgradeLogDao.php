<?php
/**
 * 升级日志
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUpgradeLogDao.php 21577 2012-12-11 08:32:31Z long.shi $
 * @package wind
 */
class PwUpgradeLogDao extends PwBaseDao {
	protected $_table = 'upgrade_log';
	protected $_dataStruct = array('id', 'type', 'data');

	/**
	 * 获取一个日志
	 *
	 * @param unknown_type $id        	
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		return $this->_get($id);
	}

	/**
	 * 根据类型获取
	 *
	 * @param unknown_type $type        	
	 * @return Ambigous <multitype:, multitype:multitype: Ambigous <multitype:,
	 *         multitype:unknown , mixed> >
	 */
	public function getByType($type) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE type = ?');
		return $this->getConnection()->createStatement($sql)->queryAll(array($type), $this->_pk);
	}

	/**
	 * 添加一个日誌
	 *
	 * @param unknown_type $id        	
	 * @param unknown_type $rule        	
	 * @param unknown_type $name        	
	 * @param unknown_type $status        	
	 * @param unknown_type $time        	
	 * @return Ambigous <boolean, number, string, rowCount>
	 */
	public function add($id, $type, $data) {
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), 
			$this->sqlSingle(array('id' => $id, 'type' => $type, 'data' => serialize($data))));
		return $this->getConnection()->execute($sql);
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
}

?>