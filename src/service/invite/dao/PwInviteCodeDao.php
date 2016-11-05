<?php

/**
 * 邀请码基本信息表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwInviteCodeDao.php 19073 2012-10-10 08:33:40Z xiaoxia.xuxx $
 * @package service.invite.dao
 */
class PwInviteCodeDao extends PwBaseDao {
	protected $_table = 'invite_code';
	protected $_pk = 'code';
	protected $_dataStruct = array('code', 'created_userid', 'invited_userid', 'ifused', 'created_time', 'modified_time');
	
	/**
	 * 根据邀请码获取该条邀请码信息
	 *
	 * @param string $code
	 * @return array
	 */
	public function getCode($code) {
		return $this->_get($code);
	}
	
	/**
	 * 根据创建用户ID获得该用户邀请成功的邀请用户
	 *
	 * @param int $uid
	 * @param int $limit 
	 * @param int $start 
	 * @return array
	 */
	public function getUsedCodeByCreatedUid($uid, $limit = 18, $start = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? AND ifused=1 ORDER BY modified_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'invited_userid');
	}
	
	/**
	 * 根据用户ID统计该用户邀请的人
	 *
	 * @param int $uid
	 * @return int
	 */
	public function countUsedCodeByCreatedUid($uid) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE created_userid=? AND ifused=1');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid));
	}
	
	/**
	 * 根据条件获得该用户的邀请码信息
	 *
	 * @param array $condition 查询条件
	 * @param int $limit 查询条数
	 * @param int $offset 开始查询的位置
	 * @return array
	 */
	public function searchCode($condition, $limit, $offset) {
		list($where, $param) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY created_time DESC %s  ', $this->getTable(), $where, $this->sqlLimit($limit, $offset));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($param);
	}
	
	/**
	 * 根据查询条件获取信息
	 *
	 * @param array $condition
	 * @return int
	 */
	public function countSearchCode($condition) {
		list($where, $param) = $this->_buildCondition($condition);
		$sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue($param);
	}
	
	/**
	 * 根据用户ID及时间统计大于这个时间的用户购买的邀请码数量
	 *
	 * @param int $uid
	 * @param int $time
	 * @return int
	 */
	public function countByUidAndTime($uid, $time) {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE `created_userid` = ? AND `created_time` > ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue(array($uid, $time));
	}
	
	/**
	 * 批量检查该code是否存在，并返回存在的codes
	 *
	 * @param array $codes
	 * @return array
	 */
	public function fetchCode($codes) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `code` IN %s', $this->getTable(), $this->sqlImplode($codes));
		return $this->getConnection()->query($sql)->fetchAll('code');
	}

	/**
	 * 添加邀请码
	 *
	 * @param array $data
	 * @return 
	 */
	public function addCode($data) {
		return $this->_add($data);
	}
	
	/**
	 * 批量添加邀请码
	 *
	 * @param array $data
	 * @return boolean
	 */
	public function batchAddCode($data) {
		$clear = array();
		foreach ($data as $_item) {
			if (!($_item = $this->_filterStruct($_item))) continue;
			$_temp = array();
			$_temp['code'] = $_item['code'];
			$_temp['created_userid'] = $_item['created_userid'];
			$_temp['created_time'] = $_item['created_time'];
			$clear[] = $_temp;
		}
		if (!$clear) return false;
		$sql = $this->_bindSql('INSERT INTO %s (`code`, `created_userid`, `created_time`) VALUES %s', $this->getTable(), $this->sqlMulti($clear));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 更新
	 *
	 * @param string $code
	 * @param array $data
	 */
	public function updateCode($code, $data) {
		return $this->_update($code, $data);
	}
	
	/**
	 * 根据邀请码删除信息
	 * 
	 * @param string $code
	 * @return boolean
	 */
	public function deleteCode($code) {
		return $this->_delete($code);
	}
	
	/**
	 * 批量删除邀请码信息
	 *
	 * @param array $codes
	 * @return boolean
	 */
	public function batchDeleteCode($codes) {
		return $this->_batchDelete($codes);
	}
	
	/**
	 * 构建查询条件
	 *
	 * @param array $condition
	 * @return array
	 */
	private function _buildCondition($condition) {
		$where = $param = array();
		foreach ($condition as $k => $v) {
			switch ($k) {
				case 'ifused':
					if (in_array($v, array(0, 1))) {
						$where[] = '`ifused` = ?';
						$param[] = $v;
					}
					break;
				case 'expire':
					$where[] = '`created_time` > ?';
					$param[] = $v;
					break;
				case 'created_userid':
					$where[] = '`created_userid` = ?';
					$param[] = $v;
					break;
				case 'invited_userid':
					$where[] = '`invited_userid` = ?';
					$param[] = $v;
					break;
				default:
					break;
			}
		}
		return $where ? array($this->_bindSql('WHERE %s', implode(' AND ', $where)) , $param) : array('', array());
	}
}