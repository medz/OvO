<?php
defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 订单数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwOrderDm.php 7431 2012-04-06 01:54:39Z jieyin $
 * @package forum
 */

class PwOrderDm extends PwBaseDm {
	
	public $id;

	public function __construct($id=0) {
		$this->id = $id;
	}

	public function setOrderNo($order_no) {
		$this->_data['order_no'] = $order_no;
		return $this;
	}
	
	public function setPrice($price) {
		$this->_data['price'] = $price;
		return $this;
	}

	public function setNumber($number) {
		$this->_data['number'] = intval($number);
		return $this;
	}

	public function setState($state) {
		$this->_data['state'] = intval($state);
		return $this;
	}

	public function setPayemail($payemail) {
		$this->_data['payemail'] = $payemail;
		return $this;
	}
	
	/**
	 * 指定使用的支付工具
	 *
	 * @param int $paymethod <1.支付宝 2.财付通 3.贝宝 4.快钱>
	 */
	public function setPaymethod($paymethod) {
		$this->_data['paymethod'] = intval($paymethod);
		return $this;
	}
	
	public function setPaytype($paytype) {
		$this->_data['paytype'] = intval($paytype);
		return $this;
	}

	public function setBuy($buy) {
		$this->_data['buy'] = intval($buy);
		return $this;
	}

	public function setCreatedUserid($userid) {
		$this->_data['created_userid'] = $userid;
		return $this;
	}

	public function setCreatedTime($time) {
		$this->_data['created_time'] = $time;
		return $this;
	}
	
	public function setExtra1($extra1) {
		$this->_data['extra_1'] = $extra1;
		return $this;
	}
	
	public function setExtra2($extra2) {
		$this->_data['extra_2'] = $extra2;
		return $this;
	}

	protected function _beforeAdd() {
		return true;
	}

	protected function _beforeUpdate() {
		return true;
	}
}
?>