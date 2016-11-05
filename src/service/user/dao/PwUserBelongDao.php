<?php

/**
 * 用户及用户组对应关系表
 * 在关联获取中，用户及用户组的关系保存在`belong`字段中传递
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserBelongDao.php 18619 2012-09-24 10:00:31Z xiaoxia.xuxx $
 * @package src.service.user.dao
 */
class PwUserBelongDao extends PwBaseDao {

	protected $_table = 'user_belong';
	protected $_dataStruct = array('uid', 'gid', 'endtime');
	
	/** 
	 * 获得某个用户的所有拥有的组
	 *
	 * @param int $uid 用户ID
	 * @return array
	 */
	public function getByUid($uid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `uid` =?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($uid), 'gid');
	}

	public function getByGid($gid) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE gid=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($gid), 'uid');
	}
	
	/** 
	 * 根据用户ID列表获取ID
	 *
	 * @param array $uids
	 * @return array
	 */
	public function fetchUserByUid($uids) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE `uid` IN %s', $this->_getTable(), $this->sqlImplode($uids));
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll();
	}
	
	/** 
	 * 删除用户数据
	 *
	 * @param int $uid 用户ID
	 * @return boolean|int
	 */
	public function delete($uid) {
		$sql = $this->_bindTable('DELETE FROM %s WHERE `uid`=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($uid));
	}
	
	/** 
	 * 更新用户组信息
	 *
	 * @param int $uid 用户ID
	 * @param array $fields 用户数据
	 * @return boolean|int
	 */
	public function edit($uid, $fields) {
		if (!($clearData = $this->_filterStruct($fields, $uid))) return false;
		$this->delete($uid);
		$sql = $this->_bindSql("REPLACE INTO %s (`uid`, `gid`, `endtime`) VALUES %s", $this->getTable(), $this->sqlMulti($clearData));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute();
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDao::_filterStruct()
	 */
	protected function _filterStruct($data, $uid = 0) {
		if (!$data) return false;
		$clearData = array();
		foreach ($data as $gid => $endTime) {
			if (0 == ($gid = intval($gid))) continue;
			$clearData[] = array($uid, $gid, $endTime);
		}
		return $clearData;
	}
	
	/** 
	 * 批量删除用户信息
	 *
	 * @param array $uids 用户ID
	 * @return boolean
	 */
	public function batchDeleteByUids($uids) {
		$sql = $this->_bindSql('DELETE FROM %s WHERE `uid` IN %s', $this->getTable(), $this->sqlImplode($uids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute();
	}
}