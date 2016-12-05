<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPush.php 17721 2012-09-08 07:45:19Z gao.wanggao $
 */
class PwDesignPush
{
    const ISSHOW = 0;
    const NEEDCHECK = 1;

    public function getPush($pushid)
    {
        $pushid = (int) $pushid;
        if ($pushid < 1) {
            return array();
        }

        return $this->_getDao()->getPush($pushid);
    }

    public function fetchPush($pushids)
    {
        if (!is_array($pushids) || !$pushids) {
            return array();
        }

        return $this->_getDao()->fetchPush($pushids);
    }

    public function searchPush(PwDesignPushSo $vo, $limit = 0, $offset = 0)
    {
        return $this->_getDao()->searchPush($vo->getData(), $vo->getOrder(), $limit, $offset);
    }

    public function countPush(PwDesignPushSo $vo)
    {
        return $this->_getDao()->countPush($vo->getData());
    }

    public function addPush(PwDesignPushDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addPush($dm->getData());
    }

    public function updatePush(PwDesignPushDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updatePush($dm->pushid, $dm->getData());
    }

    public function updateStatus($pushid, $status = true)
    {
        if (!$pushid) {
            return false;
        }
        $data = array();
        $data['status'] = (int) $status;

        return $this->_getDao()->updatePush($pushid, $data);
    }

    /**
     * 把数据修改为自动
     * Enter description here ...
     *
     * @param unknown_type $moduleid
     * @param unknown_type $order
     */
    public function updateAutoByModuleAndOrder($moduleid, $order)
    {
        if (!$moduleid || !$order) {
            return false;
        }

        return $this->_getDao()->updateAutoByModuleAndOrder($moduleid, $order);
    }

    public function updateNeedNotice($pushid, $neednotice = 0)
    {
        $data = array();
        $data['neednotice'] = (int) $neednotice;

        return $this->_getDao()->updatePush($pushid, $data);
    }

    public function deletePush($pushid)
    {
        $pushid = (int) $pushid;
        if ($pushid < 1) {
            return false;
        }

        return $this->_getDao()->deletePush($pushid);
    }

    public function deleteByModuleId($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return false;
        }

        return $this->_getDao()->deleteByModuleId($moduleid);
    }

    public function batchDelete($pushids)
    {
        if (empty($pushids) || !is_array($pushids)) {
            return false;
        }

        return $this->_getDao()->batchDelete($pushids);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignPushDao');
    }
}
