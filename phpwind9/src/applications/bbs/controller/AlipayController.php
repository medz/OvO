<?php

/**
 * 支付宝支付.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: AlipayController.php 26622 2013-04-13 02:16:20Z jieyin $
 */
class AlipayController extends PwBaseController
{
    protected $_var = [];
    protected $_conf = [];

    public function beforeAction($handlerAdapter)
    {
        parent::beforeAction($handlerAdapter);
        $this->_var = $this->getRequest()->getRequest();
        $this->_conf = Wekit::C('pay');

        if (! $this->_conf['ifopen']) {
            $this->paymsg($this->_conf['reason']);
        }
        if (! $this->_conf['alipay']) {
            $this->paymsg('onlinepay.settings.alipay.error');
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'http://notify.alipay.com/trade/notify_query.do', [
            'form_params' => [
                'notify_id' => $this->_var['notify_id'],
                'partner'   => $this->_conf['alipaypartnerID'],
            ],
        ]);
        $veryfy_result2 = $response->getBody(true);

        //兼容支付宝urlencode之后伪静态+号无法rawurldecode的处理方案
        isset($this->_var['notify_time']) && $this->_var['notify_time'] = urldecode($this->_var['notify_time']);

        ksort($this->_var);
        reset($this->_var);
        $arg = '';
        foreach ($this->_var as $key => $value) {
            if ($value && ! in_array($key, ['p', 'm', 'c', 'a', 'sign', 'sign_type'])) {
                $arg .= "$key=$value&";
            }
        }
        $veryfy_result1 = ($this->_var['sign'] == md5(substr($arg, 0, -1).$this->_conf['alipaykey'])) ? true : false;
        if (! $veryfy_result1 || ! preg_match('/true/i', $veryfy_result2)) {
            $this->paymsg('onlinepay.auth.fail', 'fail');
        }
    }

    public function run()
    {
        $order = Wekit::load('pay.PwOrder')->getOrderByOrderNo($this->_var['out_trade_no']);

        if (empty($order)) {
            $this->paymsg('onlinepay.order.exists.not');
        }
        $fee = $order['number'] * $order['price'];

        if ($fee != $this->_var['total_fee'] || $this->_var['seller_email'] != $this->_conf['alipay']) {
            $this->paymsg('onlinepay.fail');
        }
        if (! in_array($this->_var['trade_status'], ['TRADE_FINISHED', 'TRADE_SUCCESS', 'WAIT_SELLER_SEND_GOODS'])) {
            $this->paymsg('onlinepay.success');
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
        $dm->setPayemail($this->_var['buyer_email'])->setState(2)->setPaymethod(1);
        Wekit::load('pay.PwOrder')->updateOrder($dm);

        $this->paymsg('onlinepay.success');
    }

    /**
     * 显示错误信息.
     *
     * @param string $msg
     * @param string $notify
     */
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

/**
 * 测试方法
 * //TODO.
 *
 * @param unknown_type $host
 * @param unknown_type $data
 * @param unknown_type $method
 * @param unknown_type $showagent
 * @param unknown_type $port
 * @param unknown_type $timeout
 *
 * @return bool|string
 */
function PostHost($host, $data = '', $method = 'GET', $showagent = null, $port = null, $timeout = 30)
{
    //Copyright (c) 2003-2103 phpwind
    $parse = @parse_url($host);
    if (empty($parse)) {
        return false;
    }
    if ((int) $port > 0) {
        $parse['port'] = $port;
    } elseif (! $parse['port']) {
        $parse['port'] = '80';
    }

    $errnum = null;
    $parse['host'] = str_replace(['http://', 'https://'], ['', 'ssl://'], sprintf('%s://', $parse['scheme'])).$parse['host'];

    if (! $handle = @fsockopen($parse['host'], $parse['port'], $errnum, $errstr, $timeout)) {
        return false;
    }
    $method = strtoupper($method);
    $wlength = $wdata = $responseText = '';
    $parse['path'] = str_replace(['\\', '//'], '/', $parse['path'])."?$parse[query]";
    if ($method == 'GET') {
        $separator = $parse['query'] ? '&' : '';
        substr($data, 0, 1) == '&' && $data = substr($data, 1);
        $parse['path'] .= $separator.$data;
    } elseif ($method == 'POST') {
        $wlength = 'Content-length: '.strlen($data)."\r\n";
        $wdata = $data;
    }
    $write = "$method $parse[path] HTTP/1.0\r\nHost: $parse[host]\r\nContent-type: application/x-www-form-urlencoded\r\n{$wlength}Connection: close\r\n\r\n$wdata";
    //echo $write;

    @fwrite($handle, $write);
    while ($data = @fread($handle, 4096)) {
        $responseText .= $data;
    }
    @fclose($handle);
    empty($showagent) && $responseText = trim(stristr($responseText, "\r\n\r\n"), "\r\n");

    return $responseText;
}
