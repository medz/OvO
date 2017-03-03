<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 在线支付 - 支付宝支付方式.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPayService.php 7431 2012-04-06 01:54:39Z jieyin $
 */
class PwPayService
{
    public function getPayMethod($paymethod)
    {
        switch ($paymethod) {
            case 2:
                 

                return new PwTenpay();
            case 3:
                 

                return new PwPaypal();
            case 4:
                 

                return new PwBill();
            default:
                 

                return new PwAlipay();
        }
    }
}
