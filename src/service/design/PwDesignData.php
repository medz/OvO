<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignData.php 19612 2012-10-16 08:43:42Z gao.wanggao $
 * @package
 */
class PwDesignData
{
    const AUTO = 1; //自动更新
    const ISFIXED = 2; //不自动更新

    const FROM_AUTO = 0; //自动获取
    const FROM_PUSH = 1; //推送

    public function getData($dataid)
    {
        $dataid = (int) $dataid;
        if ($dataid < 1) {
            return array();
        }
        $data = $this->_getDao()->getData($dataid);
        if (!$data) {
            return array();
        }
        $standard = unserialize($data['standard']);
        $_tmp = unserialize($data['extend_info']);
        $data['title'] = $_tmp[$standard['sTitle']];
        $data['url'] = $_tmp[$standard['sUrl']];
        $data['intro'] = $_tmp[$standard['sIntro']];

        return $data;
    }

    public function fetchData($dataids)
    {
        if (!is_array($dataids)) {
            return array();
        }

        return $this->_getDao()->fetchData($dataids);
    }


    public function getDataByModuleid($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return array();
        }

        return $this->_getDao()->getDataByModuleid($moduleid);
    }

    public function fetchDataByFrom($fromids, $fromtype = self::FROM_AUTO, $datatype = self::AUTO)
    {
        if (!is_array($fromids)) {
            $fromids = (int) $fromids > 0 ? array($fromids) : array();
        }
        if (count($fromids) < 1) {
            return array();
        }

        return $this->_getDao()->fetchDataByFrom($fromids, $fromtype, $datatype);
    }

    /**
     * @param array $moduleids
     * @param int   $type
     */
    public function fetchDataByModuleid($moduleids)
    {
        if (!is_array($moduleids) || !$moduleids) {
            return array();
        }

        return $this->_getDao()->fetchDataByModuleid($moduleids);
    }

    public function searchData(PwDesignDataSo $vo, $limit = 0, $offset = 0)
    {
        return $this->_getDao()->searchData($vo->getData(), $vo->getOrder(), $limit, $offset);
    }

    public function countData(PwDesignDataSo $vo)
    {
        return $this->_getDao()->countData($vo->getData());
    }

    /**获取相同排序中最小dataid
     *
     */
    public function getMinDataIdByOrder($moduleid, $orderid)
    {
        $moduleid = (int) $moduleid;
        $orderid = (int) $orderid;
        if ($moduleid < 1 || $orderid < 1) {
            return 0;
        }

        return $this->_getDao()->getMinDataIdByOrder($moduleid, $orderid);
    }

    /**
     * 获取同某模块排序最大的data id
     * Enter description here ...
     * @param int $moduleid
     */
    public function getMaxOrderDataId($moduleid, $dataType = self::AUTO)
    {
        $moduleid = (int) $moduleid;
        $dataType = (int) $dataType;
        if ($moduleid < 1) {
            return 0;
        }

        return $this->_getDao()->getMaxOrderDataId($moduleid, $dataType);
    }

    public function getMaxOrder($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return 0;
        }

        return $this->_getDao()->getMaxOrder($moduleid);
    }

    public function addData(PwDesignDataDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addData($dm->getData());
    }

    /**
     * 把数据修改为自动
     * Enter description here ...
     * @param unknown_type $moduleid
     * @param unknown_type $order
     */
    public function updateFixedToAuto($moduleid, $order)
    {
        if (!$moduleid || !$order) {
            return false;
        }

        return $this->_getDao()->updateFixedToAuto($moduleid, $order);
    }

    public function updateData(PwDesignDataDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateData($dm->dataid, $dm->getData());
    }

    public function updateOrder($dataid, $orderid)
    {
        $dataid = (int) $dataid;
        $orderid = (int) $orderid;
        if ($dataid < 1 || $orderid < 0) {
            return false;
        }

        return $this->_getDao()->updateData($dataid, array('vieworder' => $orderid));
    }

    public function updateReservation($dataid, $reserv = 1)
    {
        $dataid = (int) $dataid;
        $reserv = (bool) $reserv;
        if ($dataid < 1) {
            return false;
        }

        return $this->_getDao()->updateData($dataid, array('is_reservation' => $reserv));
    }

    public function updateEndTime($dataid, $endtime = 0)
    {
        $dataid = (int) $dataid;
        $endtime = (int) $endtime;
        if ($dataid < 1) {
            return false;
        }

        return $this->_getDao()->updateData($dataid, array('end_time' => $endtime));
    }

    public function deleteData($dataid)
    {
        $dataid = (int) $dataid;

        return $this->_getDao()->deleteData($dataid);
    }

    public function batchDelete($dataids)
    {
        if (!is_array($dataids)) {
            $dataids = (int) $dataids > 0 ? array($dataids) : array();
        }
        if (count($dataids) < 1) {
            return false;
        }

        return $this->_getDao()->batchDelete($dataids);
    }

    public function deleteByModuleId($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return false;
        }

        return $this->_getDao()->deleteByModuleId($moduleid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignDataDao');
    }
}
