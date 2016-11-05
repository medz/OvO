<?php
/**
 * Enter description here ...
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 9, 2011
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwThreadSortDm.php 17002 2012-08-30 07:49:23Z peihong.zhangph $
 */

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:base.PwBaseDm');

class PwThreadSortDm extends PwBaseDm
{
    public function setType($type)
    {
        $this->_data['sort_type'] = $type;

        return $this;
    }

    public function setFid($fid)
    {
        $this->_data['fid'] = intval($fid);

        return $this;
    }

    public function setTid($tid)
    {
        $this->_data['tid'] = intval($tid);

        return $this;
    }

    public function setExtra($extra)
    {
        $this->_data['extra'] = intval($extra);

        return $this;
    }

    public function setCreatedtime($time)
    {
        $this->_data['created_time'] = intval($time);

        return $this;
    }

    public function setEndtime($endtime)
    {
        $this->_data['end_time'] = intval($endtime);

        return $this;
    }

    public function _beforeAdd()
    {
        if (empty($this->_data['fid']) || empty($this->_data['tid'])) {
            return new PwError('data.miss');
        }

        return true;
    }

    public function _beforeUpdate()
    {
        return true;
    }
}
