<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPage.php 18599 2012-09-24 03:02:56Z gao.wanggao $
 */
class PwDesignPage
{
    const ALL = 15;
    const NORMAL = 1;
    const SYSTEM = 2;
    const PORTAL = 8;

    public function getPage($id)
    {
        if (!$id) {
            return [];
        }

        return $this->_getDao()->getPage($id);
    }

    public function fetchPage($pageids)
    {
        if (empty($pageids) || !is_array($pageids)) {
            return [];
        }

        return $this->_getDao()->fetchPage($pageids);
    }

    public function getPageByTypeAndUnique($type = self::ALL, $unique = 0)
    {
        $type = (int) $type;
        $unique = (int) $unique;
        if ($unique < 0) {
            return [];
        }

        return $this->_getDao()->getPageByTypeAndUnique($type, $unique);
    }

    /**
     * 按路由获取所有页面
     * Enter description here ...
     *
     * @param string $router
     */
    public function getPageByRouter($router)
    {
        if (!$router) {
            return [];
        }

        return $this->_getDao()->getPageByRouter($router);
    }

    public function fetchPageByTypeUnique($type = self::ALL, $unique = [])
    {
        if (empty($unique) || !is_array($unique)) {
            return [];
        }

        return $this->_getDao()->fetchPageByTypeUnique($type, $unique);
    }

    public function getPageList($type = self::ALL, $offset = 0, $limit = 0)
    {
        return $this->_getDao()->getPageList($type, $offset, $limit);
    }

    public function countPage($type = self::ALL)
    {
        return $this->_getDao()->countPage($type);
    }

    /**
     * 查找module 所属的page页   用于后台查找.
     *
     * @param string $field
     * @param string $value
     */
    public function concatModule($value)
    {
        if (!$value) {
            return [];
        }
        $value = ','.strval($value).',';

        return $this->_getDao()->concatModule($value);
    }

    public function addPage(PwDesignPageDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addPage($dm->getData());
    }

    public function updatePage(PwDesignPageDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updatePage($dm->pageid, $dm->getData());
    }

    public function deleteNoUnique($router, $unique = 0)
    {
        if (!$router) {
            return false;
        }

        return $this->_getDao()->deleteNoUnique($router, $unique);
    }

    public function deletePage($id)
    {
        if (!$id) {
            return false;
        }

        return $this->_getDao()->delete($id);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignPageDao');
    }
}
