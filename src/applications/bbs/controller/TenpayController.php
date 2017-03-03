<?php

/**
 * 财付通支付.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: TenpayController.php 24284 2013-01-25 03:28:25Z xiaoxia.xuxx $
 */
class TenpayController extends PwBaseController
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
        if (!$this->_conf['tenpay'] || !$this->_conf['tenpaykey']) {
            $this->paymsg('onlinepay.settings.tenpay.error');
        }
        $arr = array('cmdno', 'pay_result', 'date', 'transaction_id', 'sp_billno', 'total_fee', 'fee_type', 'attach');
        $txt = '';
        foreach ($arr as $value) {
            $txt .= $value.'='.$this->_var[$value].'&';
        }
        $mac = strtoupper(md5($txt.'key='.$this->_conf['tenpaykey']));

        if ($mac != $this->_var['sign']) {
            $this->paymsg('onlinepay.auth.fail');
        }
        if ($this->_conf['tenpay'] != $this->_var['bargainor_id']) {
            $this->paymsg('onlinepay.tenpay.bargainorid.error');
        }
        if ($this->_var['pay_result'] != '0') {
            $this->paymsg('onlinepay.fail');
        }
    }

    public function run()
    {
        $order = Wekit::load('pay.PwOrder')->getOrderByOrderNo($this->_var['transaction_id']);

        if (empty($order)) {
            $this->paymsg('onlinepay.order.exists.not');
        }
        if ($order['state'] == 2) {
            $this->paymsg('onlinepay.order.paid');
        }

        $className =  
        if (class_exists($className)) {
            $class = new $className($order);
            $class->run();
        }

         
        $dm = new PwOrderDm($order['id']);
        $dm->setState(2)->setPaymethod(2);
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
