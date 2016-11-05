<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 订单数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwPayVo.php 7431 2012-04-06 01:54:39Z jieyin $
 * @package forum
 */

class PwPayVo
{
    protected $_orderNo;
    protected $_fee;
    protected $_title;
    protected $_body;

    public function setOrderNo($order_no)
    {
        $this->_orderNo = $order_no;

        return $this;
    }

    public function setFee($fee)
    {
        $this->_fee = $fee;

        return $this;
    }

    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }

    public function setBody($body)
    {
        $this->_body = $body;

        return $this;
    }

    public function getOrderNo()
    {
        return $this->_orderNo;
    }

    public function getFee()
    {
        return $this->_fee;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function getBody()
    {
        return $this->_body;
    }
}
