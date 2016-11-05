<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 帖子数据模型(insert, update)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwCreditLogDm.php 20415 2012-10-29 07:51:48Z xiaoxia.xuxx $
 * @package forum
 */

class PwCreditLogDm extends PwBaseDm
{
    public function setCtype($ctype)
    {
        $this->_data['ctype'] = intval($ctype);

        return $this;
    }

    public function setAffect($affect)
    {
        $this->_data['affect'] = intval($affect);

        return $this;
    }

    public function setLogtype($logtype)
    {
        $this->_data['logtype'] = $logtype;

        return $this;
    }

    public function setDescrip($descrip)
    {
        $this->_data['descrip'] = $descrip;

        return $this;
    }

    public function setCreatedUser($uid, $username)
    {
        $this->_data['created_userid'] = $uid;
        $this->_data['created_username'] = $username;

        return $this;
    }

    public function setCreatedTime($time)
    {
        $this->_data['created_time'] = $time;

        return $this;
    }

    protected function _beforeAdd()
    {
        $this->_data['descrip'] && $this->_data['descrip'] = strip_tags($this->_data['descrip']);

        return true;
    }

    protected function _beforeUpdate()
    {
        $this->_data['descrip'] && $this->_data['descrip'] = strip_tags($this->_data['descrip']);

        return true;
    }
}
