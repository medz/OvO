<?php

/**
 * 贝宝支付.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PaypalController.php 24284 2013-01-25 03:28:25Z xiaoxia.xuxx $
 */
class PaypalController extends PwBaseController
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
        if (!$this->_conf['paypal']) {
            $this->paymsg('onlinepay.settings.paypal.error');
        }
        if ($this->_conf['paypalkey'] != $this->_var['verifycode']) {
            $this->paymsg('onlinepay.auth.fail');
        }
    }

    public function run()
    {
        $order = Wekit::load('pay.PwOrder')->getOrderByOrderNo($this->_var['invoice']);

        if (empty($order)) {
            $this->paymsg('onlinepay.order.exists.not');
        }
        $fee = $order['number'] * $order['price'];

        if ($fee != $this->_var['mc_gross']) {
            $this->paymsg('onlinepay.fail');
        }
        if ($this->_var['payment_status'] != 'Completed') {
            $this->paymsg('onlinepay.success');
        }
        if ($order['state'] == 2) {
            $this->paymsg('onlinepay.order.paid');
        }

        $className = Wind::import('SRV:pay.srv.action.PwPayAction'.$order['paytype']);
        if (class_exists($className)) {
            $class = new $className($order);
            $class->run();
        }

        Wind::import('SRV:pay.dm.PwOrderDm');
        $dm = new PwOrderDm($order['id']);
        $dm->setState(2)->setPaymethod(3);
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
