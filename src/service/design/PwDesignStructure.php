<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignStructure.php 22339 2012-12-21 09:37:22Z gao.wanggao $
 * @package
 */
class PwDesignStructure
{
    public function getStruct($name)
    {
        if (!$name) {
            return array();
        }

        return $this->_getDao()->get($name);
    }

    public function fetchStruct($names)
    {
        if (empty($names) || !is_array($names)) {
            return array();
        }

        return $this->_getDao()->fetch($names);
    }

    public function editStruct(PwDesignStructureDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->editStruct($dm->getData());
    }

    public function replaceStruct(PwDesignStructureDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->replace($dm->getData());
    }

    public function deleteStruct($name)
    {
        if (!$name) {
            return array();
        }

        return $this->_getDao()->delete($name);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignStructureDao');
    }
}
