<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子购买记录 / ds服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadBuy.php 24066 2013-01-21 07:30:33Z jinlong.panjl $
 * @package forum
 */

class PwThreadBuy {
	
	/**
	 * 统计帖子的出售额
	 *
	 * @param int $tid 帖子id
	 * @param int $pid 回复id
	 * @return int
	 */
	public function sumCost($tid, $pid = 0) {
		if (empty($tid)) return 0;
		return $this->_getDao()->sumCost($tid, $pid);
	}

	/**
	 * 获取一条记录
	 *
	 * @param int $tid 帖子id
	 * @param int $pid 回复id
	 * @param int $uid 用户id
	 * @return array
	 */
	public function get($tid, $pid, $uid) {
		if (empty($tid) || empty($uid)) return array();
		return $this->_getDao()->get($tid, $pid, $uid);
	}

	/**
	 * 获取某帖子一楼层的所有购买记录
	 *
	 * @param int $tid 帖子id
	 * @param int $pid 回复id
	 * @return bool
	 */
	public function countByTidAndPid($tid, $pid) {
		if (empty($tid)) return array();
		return $this->_getDao()->countByTidAndPid($tid, $pid);
	}
	
	/**
	 * 获取某帖子一楼层的所有购买记录
	 *
	 * @param int $tid 帖子id
	 * @param int $pid 回复id
	 * @return bool
	 */
	public function getByTidAndPid($tid, $pid, $limit = 20, $offset = 0) {
		if (empty($tid)) return array();
		return $this->_getDao()->getByTidAndPid($tid, $pid, $limit, $offset);
	}
	
	/**
	 * 获取帖子(A)中用户(B)的所有购买记录
	 *
	 * @param int $tid 帖子(A)
	 * @param int $uid 用户(B)
	 * @return bool
	 */
	public function getByTidAndUid($tid, $uid) {
		if (empty($tid) || empty($uid)) return array();
		return $this->_getDao()->getByTidAndUid($tid, $uid);
	}
	
	/**
	 * 添加一条记录
	 *
	 * @param PwThreadBuyDm $dm 帖子购买记录数据模型
	 * return mixed
	 */
	public function add(PwThreadBuyDm $dm) {
		if (($result = $dm->beforeAdd()) !== true) {
			return $result;
		}
		return $this->_getDao()->add($dm->getData());
	}

	/**
	 * PwThreadsBuyDao
	 *
	 * @return PwThreadsBuyDao
	 */
	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwThreadsBuyDao');
	}
}