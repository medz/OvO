<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignComponent.php 21926 2012-12-17 06:08:05Z gao.wanggao $
 */
class PwDesignComponent
{
    public function getComponent($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return array();
        }

        return $this->_getDao()->getComponent($id);
    }

    public function getComponentByFlag($flag)
    {
        if (!$flag) {
            return array();
        }

        return $this->_getDao()->getComponentByFlag($flag);
    }

    public function countComponent(PwDesignComponentSo $vo)
    {
        return $this->_getDao()->countComponent($vo->getData());
    }

    public function searchComponent(PwDesignComponentSo $vo, $offset = 0, $limit = 10)
    {
        return $this->_getDao()->searchComponent($vo->getData(), $offset, $limit);
    }

    public function addComponent($flag, $name, $tpl, $issys = false)
    {
        if (!$flag || !$tpl || !$name) {
            return false;
        }
        $data['model_flag'] = $flag;
        $data['comp_name'] = $name;
        $data['comp_tpl'] = $tpl;
        if ($issys) {
            $sysId = $this->_getDao()->getMaxSysid();
            $data['sys_id'] = (int) +1;
        }

        return $this->_getDao()->addComponent($data);
    }

    public function updateComponent($id, $flag, $name, $tpl)
    {
        $id = (int) $id;
        if ($id < 1 || !$flag || !$tpl || !$name) {
            return false;
        }
        $data['model_flag'] = $flag;
        $data['comp_name'] = $name;
        $data['comp_tpl'] = $tpl;

        return $this->_getDao()->updateComponent($id, $data);
    }

    public function deleteComponent($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return false;
        }

        return $this->_getDao()->deleteComponent($id);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignComponentDao');
    }
}
