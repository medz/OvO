<?php
/**
 * Enter description here ...
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Dec 9, 2011
 *
 * @link http://www.phpwind.com
 *
 * @copyright 2011 phpwind.com
 * @license
 *
 * @version $Id: PwSpecialSortDm.php 17002 2012-08-30 07:49:23Z peihong.zhangph $
 */
defined('WEKIT_VERSION') || exit('Forbidden');

class PwSpecialSortDm extends PwBaseDm
{
    private $tid;
    private $fid;
    private $pid = 0;

    public function __construct()
    {
        //		$this->fid = intval($fid);
//		$this->tid = intval($tid);
//		$this->pid = intval($pid);
    }

    public function setType($type)
    {
        $this->_data['sort_type'] = $type;
    }

    public function setTopped($topped)
    {
        $topped = intval($topped);
        $this->_data['topped'] = $topped;
    }

    public function setFid($fid)
    {
        $fid = intval($fid);
        $this->_data['fid'] = $fid;
    }

    public function setTid($tid)
    {
        $fid = intval($tid);
        $this->_data['tid'] = $tid;
    }

    public function setPid($pid)
    {
        $pid = intval($pid);
        $this->_data['pid'] = $pid;
    }

    public function setExtra($extra)
    {
        $this->_data['extra'] = intval($extra);
    }

    public function setEndtime($endtime)
    {
        $endtime = intval($endtime);
        $this->_data['end_time'] = $endtime;
    }

    public function getFid()
    {
        return $this->fid;
    }

    public function getTid()
    {
        return $this->tid;
    }

    public function getPid()
    {
        return $this->pid;
    }

    public function _beforeAdd()
    {
        if (empty($this->_data['tid'])) {
            return new PwError('FORUM:headtopic.threaderror');
        }
    /*
        if (!$this->_data['topped']) {
            return new PwError('FORUM:headtopic.toppederror');
        }
    */
        $this->_data['created_time'] = Pw::getTime();

        return true;
    }

    public function _beforeUpdate()
    {
        if ($this->tid < 1) {
            return new PwError('FORUM:headtopic.threaderror');
        }

        return true;
    }
}
