<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * Enter description here ...
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwPostsToppedDm extends PwBaseDm
{
    public $pid;

    public function __construct($pid = 0)
    {
        $pid && $this->pid = $pid;
    }

    public function setPid($pid)
    {
        $this->_data['pid'] = intval($pid);

        return $this;
    }

    public function setTid($tid)
    {
        $this->_data['tid'] = intval($tid);

        return $this;
    }

    public function setFloor($lou)
    {
        $this->_data['floor'] = intval($lou);

        return $this;
    }

    public function setCreatedUserid($created_userid)
    {
        $this->_data['created_userid'] = intval($created_userid);

        return $this;
    }

    protected function _beforeAdd()
    {
        $this->_data['created_time'] = Pw::getTime();

        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
