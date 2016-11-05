<?php

/**
 * 帖子精华相关表
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwThreadDigestIndex.php 15975 2012-08-16 09:40:09Z xiaoxia.xuxx $
 * @package src.service.forum
 */
class PwThreadDigestIndex {
	
	/**
	 * 根据版块分类ID获取精华帖子
	 *
	 * @param int $cid 类型
	 * @param int $limit 查询的条数
	 * @param int $offset 开始查询的位置
	 * @param string $order 排序方式
	 * @return array
	 */
	public function getThreadsByCid($cid, $limit = 20, $offset = 0, $order = 'lastpost') {
		if (empty($cid)) return array();
		return $this->_getDao()->getThreadsByCid($cid, $limit, $offset, $order);
	}
	
	/**
	 * 根据版块分类ID统计精华帖子
	 *
	 * @param int $cid
	 * @return int
	 */
	public function countByCid($cid) {
		if (empty($cid)) return 0;
		return $this->_getDao()->countByCid($cid);
	}
	
	/**
	 * 根据版块ID获取该版块的精华列表
	 *
	 * @param int $fid  版块ID
	 * @param int $typeid 主题类型
	 * @param int $limit
	 * @param int $offset
	 * @param string $order
	 * @return array
	 */
	public function getThreadsByFid($fid, $typeid = 0, $limit = 20, $offset = 0, $order = 'lastpost') {
		if (empty($fid)) return 0;
		return $this->_getDao()->getThreadsByFid($fid, intval($typeid), $limit, $offset, $order);
	}
	
	/**
	 * 根据版块ID统计该版块的精华列表
	 *
	 * @param int $fid  版块ID
	 * @param int $typeid 主题类型
	 * @return int
	 */
	public function countByFid($fid, $typeid = 0) {
		if (empty($fid)) return 0;
		return $this->_getDao()->countByFid($fid, $typeid);
	}
	
	/**
	 * 添加精华
	 *
	 * @param int $tid
	 * @param array $fields
	 * @return boolean
	 */
	public function addThreadDigest(PwThreadDigestDm $dm) {
		if (true !== ($r = $dm->beforeAdd())) {
			return $r;
		}
		return $this->_getDao()->addThread($dm->tid, $dm->getData());
	}
	
	/**
	 * 根据帖子ID批量删除数据
	 *
	 * @param array $tids
	 * @return int
	 */
	public function batchDeleteThread($tids) {
		if (empty($tids)) return 0;
		return $this->_getDao()->batchDeleteThread($tids);
	}
	
	/**
	 * 批量加精
	 *
	 * @param array $dms
	 */
	public function batchAddDigest(array $dms) {
		$data = array();
		foreach ($dms as $_dm ) {
			if (!$_dm instanceof PwThreadDigestDm) {
				return new PwError('BBS:digest.dm.data.format.error');
			}
			if (($r = $_dm->beforeAdd()) instanceof PwError) {
				return $r;
			}
			$_data = $_dm->getData();
			$_data['tid'] = $_dm->tid;
			$data[] = $_data;
		}
		return $this->_getDao()->batchAddDigest($data);
	}
	
	/**
	 * 获取精华相关DAO对象
	 *
	 * @return PwThreadsDigestIndexDao
	 */
	private function _getDao() {
		return Wekit::loadDao('SRV:forum.dao.PwThreadsDigestIndexDao');
	}
}