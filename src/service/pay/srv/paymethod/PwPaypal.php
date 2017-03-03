<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 在线支付 - 贝宝支付方式.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPaypal.php 24975 2013-02-27 09:24:54Z jieyin $
 */
class PwPaypal extends PwPayAbstract
{
    public $paypal;
    public $paypal_url = 'https://www.paypal.com/cgi-bin/webscr?';
    public $paypal_key;

    public function __construct()
    {
        parent::__construct();
        $config = Wekit::C('pay');
        $this->paypal = $config['paypal'];
        $this->paypal_key = $config['paypalkey'];
        $this->baseurl = WindUrlHelper::createUrl('bbs/paypal/run');
    }

    public function check()
    {
        if (!$this->paypal || !$this->paypal_key) {
            return new PwError('onlinepay.settings.paypal.error');
        }

        return true;
    }

    public function createOrderNo()
    {
        return '3'.str_pad(Wekit::getLoginUser()->uid, 10, '0', STR_PAD_LEFT).Pw::time2str(Pw::getTime(), 'YmdHis').WindUtility::generateRandStr(5);
    }

    public function getUrl(PwPayVo $vo)
    {
        $url = $this->paypal_url;
        $param = array(
            'cmd'           => '_xclick',
            'invoice'       => $vo->getOrderNo(),
            'business'      => $this->paypal,
            'item_name'     => $vo->getTitle(),
            'item_number'   => 'phpw*',
            'amount'        => $vo->getFee(),
            'no_shipping'   => 0,
            'no_note'       => 1,
            'currency_code' => 'CNY',
            'bn'            => 'phpwind',
            'charset'       => $this->charset,
        );
        foreach ($param as $key => $value) {
            $url .= $key.'='.urlencode($value).'&';
        }

        return $url;
    }
}
