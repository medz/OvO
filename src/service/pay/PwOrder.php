<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 订单服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwOrder.php 7491 2012-04-06 10:14:44Z jieyin $
 * @package forum
 */

class PwOrder {
	
	/**
	 * 获取一个订单
	 *
	 * @param int $id 订单id
	 * return array
	 */
	public function getOrder($id) {
		if (empty($id)) return array();
		return $this->_getDao()->getOrder($id);
	}
	
	/**
	 * 获取一个订单
	 *
	 * @param string $orderno 订单号
	 * return array
	 */
	public function getOrderByOrderNo($orderno) {
		if (empty($orderno)) return array();
		return $this->_getDao()->getOrderByOrderNo($orderno);
	}
	
	public function countByUidAndType($uid, $type) {
		if (empty($uid)) return 0;
		return $this->_getDao()->countByUidAndType($uid, $type);
	}

	/**
	 * 获取用户某一类型的订单
	 *
	 * @param int $uid
	 * @param int $type
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getOrderByUidAndType($uid, $type, $limit = 20, $offset = 0) {
		if (empty($uid)) return array();
		return $this->_getDao()->getOrderByUidAndType($uid, $type, $limit, $offset);
	}

	/**
	 * 增加一个订单
	 *
	 * @param object $dm 订单数据模型
	 * return mixed
	 */
	public function addOrder(PwOrderDm $dm) {
		if (($result = $dm->beforeAdd()) !== true) {
			return $result;
		}
		return $this->_getDao()->addOrder($dm->getData());
	}
	
	/**
	 * 更新一个订单
	 *
	 * @param object $dm 订单数据模型
	 * return mixed
	 */
	public function updateOrder(PwOrderDm $dm) {
		if (($result = $dm->beforeUpdate()) !== true) {
			return $result;
		}
		return $this->_getDao()->updateOrder($dm->id, $dm->getData());
	}

	protected function _getDao() {
		return Wekit::loadDao('pay.dao.PwOrderDao');
	}
}