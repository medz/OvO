<?php
/**
 * Enter description here ...
 * 
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Oct 31, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwUserGroupsDao.php 19883 2012-10-19 06:26:36Z jieyin $
 */

class PwUserGroupsDao extends PwBaseDao {
	
	protected $_table = 'user_groups';
	protected $_pk = 'gid';
	protected $_dataStruct = array('type', 'name', 'category', 'image', 'points');
	
	/**
	 * 获取所有用户组
	 */
	public function getAllGroups() {
		$sql = $this->_bindTable("SELECT * FROM %s");
		$smt = $this->getConnection()->query($sql);
		return $smt->fetchAll('gid');
	}
	
	/**
	 * 获取一个会员组详细信息
	 *
	 * @param int $gid
	 * @return Array
	 */
	public function getGroupByGid($gid) {
		return $this->_get($gid);
	}
	
	/**
	 * 根据一组gid获取用户组
	 * 
	 * @param array $gids
	 * @return array
	 */
	public function fetchGroup($gids) {
		return $this->_fetch($gids, 'gid');
	}
	
	/**
	 * 添加用户组
	 *
	 * @param array $fields
	 */
	public function addGroup($fields) {
		return $this->_add($fields);
	}
	
	/**
	 * 更新用户组
	 *
	 * @param int $gid
	 * @param array $fields
	 */
	public function updateGroup($gid, $fields) {
		return $this->_update($gid, $fields);
	}
	
	/**
	 * 删除用户组
	 *
	 * @param int $gid
	 */
	public function deleteGroup($gid) {
		return $this->_delete($gid);
	}
	
	/**
	 * 按会员组类型获取组列表
	 *
	 * @param string $groupType
	 */
	public function getGroupsByType($groupType) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE type=?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($groupType), 'gid');
	}
	
	/**
	 * 按会员组类型获取组列表（按升级点数升序）
	 *
	 * @param string $groupType
	 */
	public function getGroupsByTypeInUpgradeOrder($groupType) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE type=? ORDER BY points');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll(array($groupType), 'gid');
	}
}