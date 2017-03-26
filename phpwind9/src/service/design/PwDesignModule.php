<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignModule.php 22555 2012-12-25 08:37:31Z gao.wanggao $
 */
class PwDesignModule
{
    const TYPE_DRAG = 1;    //拖曳模块
    const TYPE_IMPORT = 2;    //导入模块
    const TYPE_SCRIPT = 4;    //调用模块

    public function getModule($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return [];
        }

        return $this->_getDao()->getModule($moduleid);
    }

    public function fetchModule($moduleids)
    {
        if (empty($moduleids) || ! is_array($moduleids)) {
            return [];
        }

        return $this->_getDao()->fetchModule($moduleids);
    }

    public function getByPageid($pageid)
    {
        $pageid = (int) $pageid;

        return $this->_getDao()->getByPageid($pageid);
    }

    public function searchModule(PwDesignModuleSo $vo, $offset = 0, $limit = 10)
    {
        return $this->_getDao()->searchModule($vo->getData(), $vo->getOrder(), $offset, $limit);
    }

    public function countModule(PwDesignModuleSo $vo)
    {
        return $this->_getDao()->countModule($vo->getData());
    }

    public function addModule(PwDesignModuleDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addModule($dm->getData());
    }

    public function updateModule(PwDesignModuleDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateModule($dm->moduleid, $dm->getData());
    }

    public function batchUpdateIsUsed($moduleids)
    {
        if (empty($moduleids) || ! is_array($moduleids)) {
            return false;
        }

        return $this->_getDao()->batchUpdateIsUsed($moduleids);
    }

    public function deleteModule($moduleid)
    {
        $moduleid = (int) $moduleid;
        if ($moduleid < 1) {
            return false;
        }

        return $this->_getDao()->deleteModule($moduleid);
    }

    public function deleteByPageId($pageid)
    {
        if ($pageid < 1) {
            return false;
        }

        return $this->_getDao()->deleteByPageId($pageid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignModuleDao');
    }
}
