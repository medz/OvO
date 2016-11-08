<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignPushSo.php 16933 2012-08-29 09:25:02Z gao.wanggao $
 * @package
 */
class PwDesignPushSo
{
    protected $_data = array();
    protected $_order = array();

    public function getData()
    {
        return $this->_data;
    }

    public function getOrder()
    {
        return $this->_order;
    }

    public function setModuleid($id)
    {
        $this->_data['module_id'] = $id;
    }

    public function setPushUid($uid)
    {
        $this->_data['created_userid'] = $uid;
    }

    /**
     * 小于结束时间
     * Enter description here ...
     * @param unknown_type $time
     */
    public function setLtEndTime($time)
    {
        $this->_data['lt_end_time'] = $time;
    }

    /**
     * 大于结束时间
     * Enter description here ...
     * @param unknown_type $time
     */
    public function setGtEndTime($time)
    {
        $this->_data['gt_end_time'] = $time;
    }

    public function setStatus($status)
    {
        $this->_data['status'] = (int) $status;
    }

    public function orderbyPushid($asc)
    {
        $this->_order['push_id'] = (bool) $asc;
    }
}
