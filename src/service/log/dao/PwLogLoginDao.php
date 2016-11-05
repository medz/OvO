<?php
Wind::import('SRC:library.base.PwBaseDao');

/**
 * 前台用户登录错误LOG DAO服务
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogLoginDao.php 21359 2012-12-05 08:01:23Z xiaoxia.xuxx $
 * @package src.service.log.dao
 */
class PwLogLoginDao extends PwBaseDao {
	
	protected $_table = 'log_login';
	protected $_pk = 'id';
	protected $_dataStruct = array('id', 'uid', 'username', 'typeid', 'created_time', 'ip');
	
	/**
	 * 添加日志
	 *
	 * @param array $data
	 * @return int
	 */
	public function addLog($data) {
		return $this->_add($data);
	}
	
	/**
	 * 批量添加日志
	 *
	 * @param array $datas
	 * @return int
	 */
	public function batchAddLog($datas) {
		$clear = $fields = array();
		foreach ($datas as $key => $_item) {
			if (!($_item = $this->_filterStruct($_item))) continue;
			$_temp = array();
			$_temp['uid'] = $_item['uid'];
			$_temp['username'] = $_item['username'];
			$_temp['typeid'] = $_item['typeid'];
			$_temp['created_time'] = $_item['created_time'];
			$_temp['ip'] = $_item['ip'];
			$clear[] = $_temp;
		}
		if (!$clear) return false;
		$sql = $this->_bindSql('INSERT INTO %s (`uid`, `username`, `typeid`, `created_time`, `ip`) VALUES %s', $this->getTable(), $this->sqlMulti($clear));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据日志ID删除某条日志
	 *
	 * @param int $id
	 * @return int
	 */
	public function deleteLog($id) {
		return $this->_delete($id);
	}
	
	/**
	 * 根据日志ID列表删除日志
	 *
	 * @param array $ids
	 * @return int
	 */
	public function batchDeleteLog($ids) {
		return $this->_batchDelete($ids);
	}
	
	/**
	 * 清除某个时间段之前的日志
	 *
	 * @param int $time
	 * @return int
	 */
	public function clearLogBeforeDatetime($time) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE created_time < ?');
		return $this->getConnection()->createStatement($sql)->execute(array($time), true);
	}
	
	/**
	 * 根据条件搜索日志
	 *
	 * @param array $condition
	 * @param int $limit
	 * @param int $offset
	 */
	public function search($condition, $limit = 10, $offset = 0) {
		list($where, $params) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY id DESC %s', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
		return $this->getConnection()->createStatement($sql)->queryAll($params);
	}
	
	/**
	 * 根据搜索条件统计结果
	 *
	 * @param array $condition
	 * @return int
	 */
	public function countSearch($condition) {
		list($where, $params) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);
		return $this->getConnection()->createStatement($sql)->getValue($params);
	}
	
	/**
	 * 后台搜索
	 *
	 * @param array $condition
	 * @return array
	 */
	private function _buildCondition($condition) {
		$where = $params = array();
		foreach ($condition as $_k => $_v) {
			if (!$_v) continue; 
			switch($_k) {
				case 'created_username':
					$where[] = "username LIKE ?";
					$params[] = $_v . '%';
					break;
				case 'typeid':
					$where[] = "typeid = ?";
					$params[] = $_v;
					break;
				case 'ip':
					$where[] = "ip LIKE ?";
					$params[] = $_v . '%';
					break;
				case 'start_time':
					$where[] = "created_time >= ?";
					$params[] = $_v;
					break;
				case 'end_time':
					$where[] = "created_time <= ?";
					$params[] = $_v;
					break;
				default:
					break;
			}
		}
		return $where ? array(' WHERE ' . implode(' AND ', $where), $params) : array('', array());
	}
}