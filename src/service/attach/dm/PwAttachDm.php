<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

/**
 * 附件数据模型
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwAttachDm.php 4896 2012-02-27 07:54:06Z jieyin $
 * @package attach
 */

class PwAttachDm extends PwBaseDm
{
    public $aid;

    public function __construct($aid = 0)
    {
        $this->aid = $aid;
    }

    public function setAid($aid)
    {
        $this->_data['aid'] = intval($aid);

        return $this;
    }

    public function setName($name)
    {
        $this->_data['name'] = $name;

        return $this;
    }

    public function setType($type)
    {
        $this->_data['type'] = $type;

        return $this;
    }

    public function setSize($size)
    {
        $this->_data['size'] = intval($size);

        return $this;
    }

    public function setPath($path)
    {
        $this->_data['path'] = $path;

        return $this;
    }

    public function setIfthumb($ifthumb)
    {
        $this->_data['ifthumb'] = intval($ifthumb);

        return $this;
    }

    public function setCreatedUser($uid)
    {
        $this->_data['created_userid'] = $uid;

        return $this;
    }

    public function setCreatedTime($time)
    {
        $this->_data['created_time'] = $time;

        return $this;
    }

    public function setApp($app)
    {
        $this->_data['app'] = $app;

        return $this;
    }

    public function setDescrip($descrip)
    {
        $this->_data['descrip'] = $descrip;

        return $this;
    }

    protected function _beforeAdd()
    {
        return true;
    }

    protected function _beforeUpdate()
    {
        return true;
    }
}
