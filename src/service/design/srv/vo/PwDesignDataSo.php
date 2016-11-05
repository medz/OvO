<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignDataSo.php 15970 2012-08-16 09:01:29Z gao.wanggao $
 * @package
 */
class PwDesignDataSo
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

    public function setFromType($type)
    {
        $this->_data['from_type'] = (int) $type;
    }


    public function setFromid($id)
    {
        $this->_data['from_id'] = (int) $id;
    }

    public function setModuleid($id)
    {
        $this->_data['module_id'] = $id;
    }

    public function setDataType($type)
    {
        $this->_data['data_type'] = $type;
    }

    public function setEndTime($time)
    {
        $this->_data['end_time'] = $time;
    }

    public function setReservation($isreserv)
    {
        $this->_data['is_reservation'] = (int) $isreserv;
    }

    public function setVierOrder($orderid)
    {
        $this->_data['vieworder'] = (int) $orderid;
    }

    public function orderbyViewOrder($asc)
    {
        $this->_order['vieworder'] = (bool) $asc;
    }

    public function orderbyDataid($asc)
    {
        $this->_order['data_id'] = (bool) $asc;
    }
}
