<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:pay.srv.paymethod.PwPayAbstract');

/**
 * 在线支付 - 快钱支付方式.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwBill.php 24975 2013-02-27 09:24:54Z jieyin $
 */
class PwBill extends PwPayAbstract
{
    public $bill;
    public $bill_url = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm?';
    public $bill_key;

    public function __construct()
    {
        parent::__construct();
        $config = Wekit::C('pay');
        $this->bill = $config['99bill'];
        $this->bill_key = $config['99billkey'];
        $this->baseurl = WindUrlHelper::createUrl('bbs/pay99bill/run');
    }

    public function check()
    {
        if (!$this->bill || !$this->bill_key) {
            return new PwError('onlinepay.settings.99bill.error');
        }

        return true;
    }

    public function createOrderNo()
    {
        return '4'.str_pad(Wekit::getLoginUser()->uid, 10, '0', STR_PAD_LEFT).Pw::time2str(Pw::getTime(), 'YmdHis').WindUtility::generateRandStr(5);
    }

    public function getUrl(PwPayVo $vo)
    {
        strlen($this->bill) == 11 && $this->bill .= '01';
        $param = array(
            'inputCharset'   => ($this->charset == 'gbk' ? 2 : 1),
            'pageUrl'        => $this->baseurl,
            'version'        => 'v2.0',
            'language'       => 1,
            'signType'       => 1,
            'merchantAcctId' => $this->bill,
            'payerName'      => 'admin',
            'orderId'        => $vo->getOrderNo(),
            'orderAmount'    => ($vo->getFee() * 100),
            'orderTime'      => Pw::time2str(Pw::getTime(), 'YmdHis'),
            'productName'    => $vo->getBody(),
            'productNum'     => 1,
            'payType'        => '00',
            'redoFlag'       => 1,
        );
        $url = $this->bill_url;
        $arg = '';
        foreach ($param as $key => $value) {
            $value = trim($value);
            if (strlen($value) > 0) {
                $arg .= "$key=$value&";
                $url .= "$key=".urlencode($value).'&';
                //$inputMsg .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
            }
        }
        $url .= 'signMsg='.strtoupper(md5($arg.'key='.$this->bill_key));

        return $url;
    }
}
