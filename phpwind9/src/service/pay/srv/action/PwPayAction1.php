<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 在线支付 - 积分购买.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPayAction1.php 18618 2012-09-24 09:31:00Z jieyin $
 */
class PwPayAction1
{
    protected $_order;
    protected $_conf;

    public function __construct($order)
    {
        $this->_order = $order;
        $this->_conf = Wekit::C('credit', 'recharge');
    }

    public function run()
    {
        $rmbrate = $this->_conf[$this->_order['buy']]['rate'];
        ! $rmbrate && $rmbrate = 10;
        $num = round($this->_order['price'] * $rmbrate);

        /* @var $creditBo PwCreditBo */
        $creditBo = PwCreditBo::getInstance();
        $creditBo->addLog('olpay_credit', [$this->_order['buy'] => $num], PwUserBo::getInstance($this->_order['created_userid']), [
            'number' => $this->_order['price'],
        ]);
        $creditBo->set($this->_order['created_userid'], $this->_order['buy'], $num);

        //发送通知
        $params = [];
        $params['change_type'] = 'pay';
        $params['credit'] = $creditBo->cType[$this->_order['buy']];
        $params['num'] = $num;
        $params['unit'] = $creditBo->cUnit[$this->_order['buy']];
        $params['price'] = $this->_order['price'];

        /* @var $notice PwNoticeService */
        $notice = Wekit::load('SRV:message.srv.PwNoticeService');
        $notice->sendNotice($this->_order['created_userid'], 'credit', $this->_order['created_userid'], $params);
    }
}
