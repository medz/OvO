<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 订单数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwTopicRecycleDm.php 14354 2012-07-19 10:36:06Z jieyin $
 * @package forum
 */

class PwTopicRecycleDm extends PwBaseDm
{
    public $tid;

    public function __construct($tid = 0)
    {
        $this->tid = $tid;
    }

    public function setTid($tid)
    {
        $this->_data['tid'] = $tid;

        return $this;
    }

    public function setFid($fid)
    {
        $this->_data['fid'] = $fid;

        return $this;
    }

    public function setOperateTime($time)
    {
        $this->_data['operate_time'] = $time;

        return $this;
    }

    public function setOperateUsername($username)
    {
        $this->_data['operate_username'] = $username;

        return $this;
    }

    public function setReason($reason)
    {
        $this->_data['reason'] = $reason;

        return $this;
    }

    protected function _beforeAdd()
    {
        if (!$this->_data['tid'] || !$this->_data['fid']) {
            return false;
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
