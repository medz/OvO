<?php
/**
 * 用户禁止信息表
 * 用户行为禁止：
 *    1: 禁止用户发言
 *    2: 禁止用户使用头像
 *    4: 禁止用户使用签名
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBanDao.php 16415 2012-08-23 07:53:40Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserBanDao extends PwBaseDao {
	protected $_table = 'user_ban';
	protected $_pk = 'id';
	protected $_dataStruct = array('id', 'uid', 'typeid', 'fid', 'end_time', 'created_time', 'created_userid', 'reason');

	/**
	 * 获取用户ID禁止信息
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getBanInfo($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'typeid');
	}
	
	/**
	 * 根据用户的禁止类型获取用户iD禁止信息
	 *
	 * @param int $uid 用户ID
	 * @param int $typeid 禁止类型
	 * @return array
	 */
	public function getBanInfoByTypeid($uid, $typeid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND `typeid` & ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid, $typeid), 'typeid');
	}
	
	/**
	 * 根据禁止类型及禁止类型中的具体ID获得用户uid的禁止信息
	 *
	 * @param int $uid
	 * @param int $typeid
	 * @param int $fid
	 * @return array
	 */
	public function getBanInfoByTypeidAndFid($uid, $typeid, $fid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE uid=? AND `typeid` & ? AND `fid` IN (0, ?)');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid, $typeid, $fid), 'typeid');
	}
	
	/**
	 * 根据用户ID列表及版块ID获得用户禁止信息
	 *
	 * @param array $uids
	 * @param int $typeid 用户禁止类型
	 * @return array
	 */
	public function fetchBanInfoByUid($uids, $typeid) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE uid IN %s AND `typeid` & ?', $this->getTable(), $this->sqlImplode($uids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($typeid), 'id');
	}
	
	/**
	 * 根据禁止ID列表获取禁止数据
	 *
	 * @param array $ids
	 * @return array
	 */
	public function fetchBanInfo($ids) {
		return $this->_fetch($ids, $this->_pk);
	}
	
	/** 
	 * 添加用户禁止记录
	 *
	 * @param array $data 禁言信息
	 * @return int
	 */
	public function addBanInfo($data) {
		if (!($data = $this->_filterStruct($data))) return false;
		$sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 批量禁止用户
	 *
	 * @param array $data
	 * @return array
	 */
	public function batchAddBanInfo($data) {
		$clear = array();
		foreach ($data as $key => $_item) {
			if (!($_item = $this->_filterStruct($_item))) continue;
			$_temp = array();
			$_temp['uid'] = $_item['uid'];
			$_temp['typeid'] = $_item['typeid'];
			$_temp['fid'] = $_item['fid'];
			$_temp['end_time'] = $_item['end_time'];
			$_temp['created_time'] = $_item['created_time'];
			$_temp['created_userid'] = $_item['created_userid'];
			$_temp['reason'] = $_item['reason'];
			$clear[] = $_temp;
		}
		if (!$clear) return false;
		$sql = $this->_bindSql('REPLACE INTO %s (`uid`, `typeid`, `fid`, `end_time`, `created_time`, `created_userid`, `reason`) VALUES %s', $this->getTable(), $this->sqlMulti($clear));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据禁止ID列表删除禁止记录
	 *
	 * @param array $ids 
	 * @return boolean
	 */
	public function batchDelete($ids) {
		return $this->_batchDelete($ids);
	}

	/**
	 * 根据用户ID删除用户的屏蔽信息
	 *
	 * @param int $uid
	 * @return int
	 */
	public function deleteByUid($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($uid));
	}
	
	/** 
	 * 根据用户ID批量删除该用户信息
	 *
	 * @param array $uids 用户ID列表
	 * @return int
	 */
	public function batchDeleteByUids($uids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` IN %s', $this->getTable(), $this->sqlImplode($uids));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 根据条件统计数据
	 *
	 * @param array $condition
	 * @return int
	 */
	public function countByCondition($condition) {
		list($where, $params) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue($params);
	}
	
	/**
	 * 根据条件检索数据
	 *
	 * @param array $condition 查询条件
	 * @param int $limit 返回条数
	 * @param int $start 记录查询开始
	 * @return array
	 */
	public function fetchBanInfoByCondition($condition, $limit = 10, $start = 0) {
		list($where, $params) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT * FROM %s %s %s', $this->getTable(), $where, $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($params, 'id');
	}
	
	/**
	 * 构建搜索条件
	 * 搜索条件支持：
	 * <pre>
	 *   array('username/uid' => '', 'created_userid' => '', 'start_time' => '时间戳', 'end_time' => '时间戳');
	 * </pre>
	 *
	 * @param array $condition
	 * @return string
	 */
	private function _buildCondition($condition) {
		if (!$condition) return array('', array());
		$where = $params = array();
		foreach ($condition as $key => $value) {
			if (!$value && $value !== 0) continue;
			switch ($key) {
				case 'uid':
					$where[] = '`uid`=?';
					$params[] = $value;
					break;
				case 'created_userid':
					$where[] = '`created_userid` = ?';
					$params[] = $value;
					break;
				case 'start_time':
					$where[] = '`created_time` >= ?';
					$params[] = $value;
					break;
				case 'end_time':
					$where[] = '`created_time` <= ?';
					$params[] = $value;
					break;
			}
		}
		return $where ?  array(' WHERE ' . implode(' AND ', $where), $params) : array('', array());
	}
}