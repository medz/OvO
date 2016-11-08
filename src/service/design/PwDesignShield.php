<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignShield.php 20146 2012-10-24 02:51:25Z gao.wanggao $
 * @package
 */

class PwDesignShield
{
    public function getShieldByModuleId($moduleid)
    {
        $moduleid = (int) $moduleid;

        return $this->_getDao()->getShieldByModuleId($moduleid);
    }

    public function fetchByFromidsAndApp($fromids, $fromapp)
    {
        if (!is_array($fromids) || !$fromids || !$fromapp) {
            return array();
        }

        return $this->_getDao()->fetchByFromidsAndApp($fromids, $fromapp);
    }

    public function getShieldList($moduleid = 0, $offset = 0, $limit = 10)
    {
        $moduleid = (int) $moduleid;
        $offset = (int) $offset;
        $limit = (int) $limit;

        return $this->_getDao()->getShieldList($moduleid, $offset, $limit);
    }

    public function countShield($moduleid = 0)
    {
        $moduleid = (int) $moduleid;

        return $this->_getDao()->countShield($moduleid);
    }

    public function addShield($app, $fromid, $moduleid = 0, $title = '', $url = '')
    {
        $data = array();
        $data['from_app'] = $app;
        $data['from_id'] = (int) $fromid;
        $data['module_id'] = (int) $moduleid;
        $data['shield_title'] = $title;
        $data['shield_url'] = $url;

        return $this->_getDao()->add($data);
    }

    public function deleteShield($id)
    {
        $id = (int) $id;
        if (!$id) {
            return false;
        }

        return $this->_getDao()->delete($id);
    }

    /**
     * 反转删除
     * Enter description here ...
     * @param array $ids
     */
    public function antiDelete($ids)
    {
        if (!is_array($ids) || !$ids) {
            return false;
        }

        return $this->_getDao()->antiDelete($ids);
    }

    public function deleteByModuleId($moduleid)
    {
        $moduleid = (int) $moduleid;

        return $this->_getDao()->deleteByModuleId($moduleid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignShieldDao');
    }
}
