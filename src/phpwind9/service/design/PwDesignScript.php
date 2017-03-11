<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignScript.php 16031 2012-08-17 08:18:58Z gao.wanggao $
 */
class PwDesignScript
{
    public function getScript($moduleId)
    {
        $moduleId = (int) $moduleId;
        if ($moduleId < 1) {
            return array();
        }

        return $this->_getDao()->get($moduleId);
    }

    public function addScript($moduleId, $token, $times)
    {
        if ($moduleId < 1) {
            return false;
        }
        $data['module_id'] = $moduleId;
        $data['token'] = $token;
        $data['view_times'] = $times;

        return $this->_getDao()->add($data);
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
        return Wekit::loadDao('design.dao.PwDesignScriptDao');
    }
}
