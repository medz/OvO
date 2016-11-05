<?php
/**
 * 应用安装日志表
 *
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com/license.php
 * @version $Id$
 * @package wind
 */
class PwApplicationLogDao extends PwBaseDao {
	protected $_table = 'application_log';
	protected $_dataStruct = array('app_id', 'log_type', 'created_time', 'modified_time', 'data');

	/**
	 * 添加应用安装日志
	 *
	 * @param array $fields
	 * @return boolean|Ambigous <rowCount, boolean, number>
	 */
	public function add($fields) {
		if (!$fields = $this->_filterStruct($fields)) return false;
		$sql = $this->_bindTable('INSERT INTO %s SET ') . $this->sqlSingle($fields);
		return $this->getConnection()->createStatement($sql)->execute();
	}

	/**
	 * 批量添加
	 *
	 * @param array $fields
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function batchAdd($fields) {
		foreach ($fields as $key => $value) {
			$_tmp = array();
			$_tmp['app_id'] = $value['app_id'];
			$_tmp['log_type'] = $value['log_type'];
			$_tmp['created_time'] = intval($value['created_time']);
			$_tmp['modified_time'] = intval($value['modified_time']);
			$_tmp['data'] = $value['data'];
			$fields[$key] = $_tmp;
		}
		$sql = $this->_bindTable('REPLACE INTO %s (`app_id`,`log_type`,`created_time`,`modified_time`,`data`) VALUES ') . $this->sqlMulti(
			$fields);
		return $this->getConnection()->createStatement($sql)->execute();
	}

	/**
	 * 根据ID删除应用安装日志
	 *
	 * @param string $appid
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function delByAppId($appid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE app_id=?');
		return $this->getConnection()->createStatement($sql)->execute(array($appid));
	}

	/**
	 * 根据ID批量删除应用安装日志
	 *
	 * @param array $appids
	 * @return 
	 */
	public function batchDelByAppId($appids) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE app_id IN ') . $this->sqlImplode($appids);
		return $this->getConnection()->createStatement($sql)->execute();
	}

	/**
	 * 根据APP_ID获取应用安装日志
	 *
	 * @param string $appid
	 * @return Ambigous <multitype:, multitype:multitype: Ambigous <multitype:, multitype:unknown , mixed> >
	 */
	public function fetchByAppId($appid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE app_id=?');
		return $this->getConnection()->createStatement($sql)->queryAll(array($appid));
	}
}

?>