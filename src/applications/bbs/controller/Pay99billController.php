<?php

/**
 * 快钱支付.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: Pay99billController.php 24284 2013-01-25 03:28:25Z xiaoxia.xuxx $
 */
class Pay99billController extends PwBaseController
{
    protected $_var = array();
    protected $_conf = array();

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $this->_var = $this->getRequest()->getRequest();
        $this->_conf = Wekit::C('pay');

        if (!$this->_conf['ifopen']) {
            $this->paymsg($this->_conf['reason']);
        }
        if (!$this->_conf['99bill'] || !$this->_conf['99billkey']) {
            $this->paymsg('onlinepay.settings.99bill.error');
        }
        strlen($this->_conf['99bill']) == 11 && $this->_conf['99bill'] .= '01';

        $arr = array('payType', 'bankId', 'orderId', 'orderTime', 'orderAmount', 'dealId', 'bankDealId', 'dealTime', 'payAmount', 'fee', 'payResult', 'errCode');

        $txt = 'merchantAcctId='.$this->_conf['99bill'].'&version=v2.0&language=1&signType=1';
        foreach ($arr as $value) {
            $this->_var[$value] = trim($this->_var[$value]);
            if (strlen($this->_var[$value]) > 0) {
                $txt .= '&'.$value.'='.$this->_var[$value];
            }
        }
        $mac = strtoupper(md5($txt.'&key='.$this->_conf['99billkey']));

        if ($mac != strtoupper(trim($this->_var['signMsg']))) {
            $this->paymsg('onlinepay.auth.fail');
        }
        if ($this->_var['payResult'] != '10') {
            $this->paymsg('onlinepay.success');
        }
    }

    public function run()
    {
        $order = Wekit::load('pay.PwOrder')->getOrderByOrderNo($this->_var['orderId']);

        if (empty($order)) {
            $this->paymsg('onlinepay.order.exists.not');
        }
        if ($order['state'] == 2) {
            $this->paymsg('onlinepay.order.paid');
        }

        $className = 'PwPayAction'.$order['paytype'];
        if (class_exists($className)) {
            $class = new $className($order);
            $class->run();
        }

         
        $dm = new PwOrderDm($order['id']);
        $dm->setState(2)->setPaymethod(4);
        Wekit::load('pay.PwOrder')->updateOrder($dm);

        $this->paymsg('onlinepay.success');
    }

    protected function paymsg($msg, $notify = 'success')
    {
        if (empty($_POST)) {
            if ('onlinepay.success' == $msg) {
                $this->showMessage($msg, 'profile/credit/order', 2);
            }
            $this->showError($msg, 'profile/credit/order', 2);
        }
        exit($notify);
    }
}
