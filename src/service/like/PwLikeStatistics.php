<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLikeStatistics.php 8980 2012-05-01 06:22:59Z gao.wanggao $ 
 * @package 
 */
class PwLikeStatistics {
	
	/**
	 * 获取内容
	 * 
	 * @param string $signkey
	 */
	public function getLikeStatistics($signkey) {
		return $this->_getLikeStatisticsDao()->getInfo($signkey);
	}
	
	/**
	 * 获取内容
	 * 
	 * @param string $signkey
	 * @param int $likeid
	 */
	public function getInfoByLikeid($signkey, $likeid) {
		return $this->_getLikeStatisticsDao()->getInfoByLikeid($signkey, $likeid);
	}
	
	/**
	 * 获取多个key内容
	 * 
	 * @param array $signkeys
	 */
	public function fetchLikeStatistics($signkeys) {
		return $this->_getLikeStatisticsDao()->fetchInfo($signkeys);
	}
	
	/**
	 * 分页获取内容
	 * 
	 * @param string $signkey
	 * @param int $start
	 * @param int $limit
	 * @param bool $isthread 用于过滤主题和回复
	 */
	public function getInfoList($signkey, $start = 0, $limit = 10, $isthread = false) {
		$start = (int)$start;
		$limit = (int)$limit;
		return $this->_getLikeStatisticsDao()->getInfoList($signkey, $start, $limit, $isthread);
	}
	
	/**
	 * 获取该key中的最小统计数
	 * 
	 * @param string $signkey
	 */
	public function getMinInfo($signkey) {
		return $this->_getLikeStatisticsDao()->getMinInfo($signkey);
	}
	
	/**
	 * 统计该key的数量
	 * 
	 * @param string $signkey
	 */
	public function countSignkey($signkey) {
		return $this->_getLikeStatisticsDao()->countSignkey($signkey);
	}
	
	/**
	 * 添加内容
	 * 
	 * @param PwLikeStatisticsDm $dm
	 */
	public function addInfo(PwLikeStatisticsDm $dm) {
		$data = $dm->getData();
		$info = $this->getInfoByLikeid($data['signkey'], $data['likeid']);
		if ($info) {
			return $this->_getLikeStatisticsDao()->updateInfo($data);
		}else {
			return $this->_getLikeStatisticsDao()->addInfo($data);
		}
	}
	
	/**
	 * 删除内容
	 * 
	 * @param string $key
	 */
	public function deleteInfo($key) {
		return $this->_getLikeStatisticsDao()->deleteInfo($key);
	}
	

	private function _getLikeStatisticsDao() {
		return Wekit::loadDao('like.dao.PwLikeStatisticsDao');
	}
}
?>