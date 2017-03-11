<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 订单数据模型.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReplyRecycleDm.php 14354 2012-07-19 10:36:06Z jieyin $
 */
class PwReplyRecycleDm extends PwBaseDm
{
    public $pid;

    public function __construct($pid = 0)
    {
        $this->pid = $pid;
    }

    public function setPid($pid)
    {
        $this->_data['pid'] = $pid;

        return $this;
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
        if (!$this->_data['pid'] || !$this->_data['tid'] || !$this->_data['fid']) {
            return false;
        }

        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
