<?php
/**
 * 搜索用户DS
 */
class PwUserSearch 
{
	/**
	 * 根据条件搜索用户
	 *
	 * @param PwUserSo $vo
	 * @param int $limit 查询条数
	 * @param int $start 开始查询的位置
	 * @return array
	 */
	public function searchUser(PwUserSo $vo, $limit = 10, $start = 0)
	{
		return $this->_getDao()->searchUser($vo->getData(), $limit, $start, $vo->getOrderby());
	}
	
	/**
	 * 总是获取相关三张表的所有数据
	 * 门户数据获取
	 *
	 * @param PwUserSo $vo
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function searchUserAllData(PwUserSo $vo, $limit = 10, $start = 0)
	{
		return $this->_getDao()->searchUserAllData($vo->getData(), $limit, $start, $vo->getOrderby());
	}
	
	/**
	 * 根据条件统计用户
	 *
	 * @param PwUserSo $vo
	 * @return array
	 */
	public function countSearchUser(PwUserSo $vo)
	{
		return $this->_getDao()->countSearchUser($vo->getData());
	}
	
	/**
	 * 获取用户搜索的DAO
	 *
	 * @return PwUserSearchDao
	 */
	private function _getDao()
	{
		return Wekit::loadDao('EXT:search.service.dao.PwUserSearchDao');
	}
}