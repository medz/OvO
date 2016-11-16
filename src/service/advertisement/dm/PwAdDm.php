<?php

defined('WEKIT_VERSION') || exit('Forbidden');


/**
 *
 * 广告位数据模型
 *
 * @author Zhu Dong <zhudong0808@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z zhudong $
 * @package wind
 */
class PwAdDm extends PwBaseDm
{
    public $pid = 0;
    public $_data = array();

    public function __construct($pid = 0)
    {
        $this->pid = $pid;
    }

    public function setPid($pid)
    {
        $this->_data['pid'] = intval($pid);

        return $this;
    }

    public function setIdentifier($identifier)
    {
        $this->_data['identifier'] = $identifier;

        return $this;
    }

    public function setType($typeId)
    {
        $this->_data['type_id'] = intval($typeId);

        return $this;
    }

    public function setWidth($width)
    {
        $this->_data['width'] = intval($width);

        return $this;
    }

    public function setHeight($height)
    {
        $this->_data['height'] = intval($height);

        return $this;
    }

    public function setStatus($status)
    {
        $this->_data['status'] = intval($status);

        return $this;
    }

    public function setSchedule($schedule)
    {
        $this->_data['schedule'] = $schedule;

        return $this;
    }

    public function setShowType($showType)
    {
        $this->_data['show_type'] = $showType;

        return $this;
    }

    public function setCondition($condition)
    {
        $this->_data['condition'] = $condition;

        return $this;
    }

    protected function _beforeUpdate()
    {
        if (empty($this->_data)) {
            return false;
        }

        return true;
    }

    protected function _beforeAdd()
    {
        if (empty($this->_data)) {
            return false;
        }

        return true;
    }
}
