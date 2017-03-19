<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 在线支付 - 支付宝支付方式.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAlipay.php 26892 2013-04-18 01:24:54Z yetianshi $
 */
class PwAlipay extends PwPayAbstract
{
    public $alipay;
    public $alipay_url = '';
    public $alipay_key;
    public $alipay_partnerID;
    public $alipay_interface;

    public function __construct()
    {
        parent::__construct();
        $config = Wekit::C('pay');
        $this->alipay = $config['alipay'];
        $this->alipay_key = $config['alipaykey'];
        $this->alipay_partnerID = $config['alipaypartnerID'];
        $this->alipay_interface = $config['alipayinterface'];
        $this->baseurl = WindUrlHelper::createUrl('bbs/alipay/run');
        $this->alipay_url = 'https://mapi.alipay.com/gateway.do?';
    }

    public function check()
    {
        if (!$this->alipay || !$this->alipay_partnerID || !$this->alipay_key) {
            return new PwError('onlinepay.settings.alipay.error');
        }

        return true;
    }

    public function createOrderNo()
    {
        return '1'.str_pad(Wekit::getLoginUser()->uid, 10, '0', STR_PAD_LEFT).Pw::time2str(Pw::getTime(), 'YmdHis').WindUtility::generateRandStr(5);
    }

    public function getUrl(PwPayVo $vo)
    {
        $param = [
            'payment_type' => '1',

            '_input_charset' => $this->charset,
            'seller_email'   => $this->alipay,
            'notify_url'     => $this->baseurl,
            'return_url'     => $this->baseurl,

            'out_trade_no' => $vo->getOrderNo(),
            'subject'      => $vo->getTitle(),
            'body'         => $vo->getBody(),

            'extend_param' => 'isv^pw11',
            //'extra_common_param' => ''//$this->formatExtra($extra),
        ];

        if (!$this->alipay_interface) {
            $param['service'] = 'trade_create_by_buyer';
            $param['price'] = $vo->getFee();
            $param['quantity'] = '1';
            $param['logistics_fee'] = '0.00';
            $param['logistics_type'] = 'EXPRESS';
            $param['logistics_payment'] = 'SELLER_PAY';
        } else {
            $param['service'] = 'create_direct_pay_by_user';
            $param['total_fee'] = $vo->getFee();
        }

        return $this->_bulidUrl($this->alipay_url, $this->alipay_partnerID, $this->alipay_key, $param);
    }

    protected function _bulidUrl($url, $partnerID, $partnerKey, $param)
    {
        $param['partner'] = $partnerID;
        ksort($param);
        reset($param);
        $arg = '';
        foreach ($param as $key => $value) {
            if ($value) {
                $url .= "$key=".urlencode($value).'&';
                $arg .= "$key=$value&";
            }
        }
        $url .= 'sign='.md5(substr($arg, 0, -1).$partnerKey).'&sign_type=MD5';

        return $url;
    }
}
