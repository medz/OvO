<?php

/**
 * 搜索话题
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwTagSearch {
	
	/**
	 * 根据条件搜索用户
	 *
	 * @param PwUserSo $vo
	 * @param int $limit 查询条数
	 * @param int $start 开始查询的位置
	 * @return array
	 */
	public function searchTag(PwTagSo $vo, $limit = 10, $start = 0) {
		return $this->_getDao()->searchTag($vo->getData(), $vo->getOrderby(), $limit, $start);
	}
	
	/**
	 * 根据条件统计用户
	 *
	 * @param PwUserSo $vo
	 * @return array
	 */
	public function countSearchTag(PwTagSo $vo) {
		return $this->_getDao()->countSearchTag($vo->getData());
	}
	
	/**
	 * 获取搜索的DAO
	 *
	 * @return PwTagSearchDao
	 */
	private function _getDao() {
		return Wekit::loadDao('tag.dao.PwTagSearchDao');
	}
}