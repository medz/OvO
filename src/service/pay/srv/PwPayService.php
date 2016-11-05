<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 在线支付 - 支付宝支付方式
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPayService.php 7431 2012-04-06 01:54:39Z jieyin $
 * @package forum
 */

class PwPayService {
	
	public function getPayMethod($paymethod) {
		switch ($paymethod) {
			case 2:
				Wind::import('SRV:pay.srv.paymethod.PwTenpay');
				return new PwTenpay();
			case 3:
				Wind::import('SRV:pay.srv.paymethod.PwPaypal');
				return new PwPaypal();
			case 4:
				Wind::import('SRV:pay.srv.paymethod.PwBill');
				return new PwBill();
			default:
				Wind::import('SRV:pay.srv.paymethod.PwAlipay');
				return new PwAlipay();
		}
	}
}