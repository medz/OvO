<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:pay.srv.paymethod.PwPayAbstract');

/**
 * 在线支付 - 财付通支付方式
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTenpay.php 24975 2013-02-27 09:24:54Z jieyin $
 * @package forum
 */

class PwTenpay extends PwPayAbstract
{
    public $tenpay;
    public $tenpay_url = 'https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi?';
    public $tenpay_key;

    public function __construct()
    {
        parent::__construct();
        $config = Wekit::C('pay');
        $this->tenpay = $config['tenpay'];
        $this->tenpay_key = $config['tenpaykey'];
        $this->baseurl = WindUrlHelper::createUrl('bbs/tenpay/run');
    }

    public function check()
    {
        if (!$this->tenpay || !$this->tenpay_key) {
            return new PwError('onlinepay.settings.tenpay.error');
        }

        return true;
    }

    public function createOrderNo()
    {
        return $this->tenpay.Pw::time2str(Pw::getTime(), 'YmdHis').str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function getUrl(PwPayVo $vo)
    {
        $strTransactionId = $vo->getOrderNo();
        $strBillDate = substr($strTransactionId, 10, 8);
        $strSpBillNo = substr($strTransactionId, -10);

        $param = array(
            'cmdno' => '1',
            'date' => $strBillDate,
            'bargainor_id' => $this->tenpay,
            'transaction_id' => $strTransactionId,
            'sp_billno' => $strSpBillNo,
            'total_fee' => $vo->getFee() * 100,
            'bank_type' => 0,
            'fee_type' => 1,
            'return_url' => $this->baseurl,
            'attach' => 'my_magic_string',
            'desc' => Pw::convert($vo->getTitle(), 'gbk'),
        );

        return $this->_bulidUrl($this->tenpay_url, $this->tenpay_key, $param);
    }

    protected function _bulidUrl($url, $tenpayKey, $param)
    {
        $arg = '';
        foreach ($param as $key => $value) {
            if ($value) {
                $url .= "$key=".urlencode($value).'&';
                $key != 'desc' && $arg .= "$key=$value&";
            }
        }
        $url .= 'sign='.strtoupper(md5($arg.'key='.$tenpayKey));

        return $url;
    }
}
