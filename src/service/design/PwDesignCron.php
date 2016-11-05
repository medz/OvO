<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignCron.php 10783 2012-05-30 05:25:30Z gao.wanggao $
 * @package
 */
class PwDesignCron
{
    public function getCron($moduleId)
    {
        $moduleId = (int) $moduleId;
        if ($moduleId < 1) {
            return array();
        }

        return $this->_getDao()->get($moduleId);
    }

    public function fetchCron($moduleIds)
    {
        if (!is_array($moduleIds) || !$moduleIds) {
            return array();
        }

        return $this->_getDao()->fetch($moduleIds);
    }

    public function addCron($moduleId, $time)
    {
        if ($moduleId < 1 || $time < 1) {
            return false;
        }
        $data['module_id'] = $moduleId;
        $data['created_time'] = $time;

        return $this->_getDao()->add($data);
    }

    public function batchAdd($data)
    {
        if (!is_array($data) || !$data) {
            return false;
        }

        return $this->_getDao()->batchAdd($data);
    }

    public function getAllCron()
    {
        return $this->_getDao()->getAllCron();
    }

    public function deleteCron($moduleId)
    {
        $moduleId = (int) $moduleId;
        if ($moduleId < 1) {
            return false;
        }

        return $this->_getDao()->delete($moduleId);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignCronDao');
    }
}
