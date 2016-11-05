<?php
/**
 * 搜索用户DS
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserSearch.php 16347 2012-08-22 09:16:36Z xiaoxia.xuxx $
 * @package service.user
 */
class PwUserSearch {
	
	/**
	 * 根据条件搜索用户
	 *
	 * @param PwUserSo $vo
	 * @param int $limit 查询条数
	 * @param int $start 开始查询的位置
	 * @return array
	 */
	public function searchUser(PwUserSo $vo, $limit = 10, $start = 0) {
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
	public function searchUserAllData(PwUserSo $vo, $limit = 10, $start = 0) {
		return $this->_getDao()->searchUserAllData($vo->getData(), $limit, $start, $vo->getOrderby());
	}
	
	/**
	 * 根据条件统计用户
	 *
	 * @param PwUserSo $vo
	 * @return array
	 */
	public function countSearchUser(PwUserSo $vo) {
		return $this->_getDao()->countSearchUser($vo->getData());
	}
	
	/**
	 * 获取用户搜索的DAO
	 *
	 * @return PwUserSearchDao
	 */
	private function _getDao() {
		return Wekit::loadDao('user.dao.PwUserSearchDao');
	}
}