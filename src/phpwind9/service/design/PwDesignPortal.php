<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: long.shi $>.
 *
 * @author $Author: long.shi $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignPortal.php 18647 2012-09-25 07:36:25Z long.shi $
 */
class PwDesignPortal
{
    public function getPortal($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return [];
        }

        return $this->_getDao()->get($id);
    }

    public function getPortalByDomain($domain)
    {
        if (!$domain) {
            return [];
        }

        return $this->_getDao()->getByDomain($domain);
    }

    public function countPortalByPagename($pagename)
    {
        if (!$pagename) {
            return false;
        }

        return $this->_getDao()->countPortalByPagename($pagename);
    }

    public function fetchPortal($ids)
    {
        if (!is_array($ids) || !$ids) {
            return [];
        }

        return $this->_getDao()->fetch($ids);
    }

    public function searchPortal(PwDesignPortalSo $vo, $offset = 0, $limit = 10)
    {
        return $this->_getDao()->searchPortal($vo->getData(), $offset, $limit);
    }

    public function countPartal(PwDesignPortalSo $vo)
    {
        return $this->_getDao()->countPartal($vo->getData());
    }

    public function addPortal(PwDesignPortalDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->add($dm->getData());
    }

    public function updatePortal(PwDesignPortalDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->update($dm->id, $dm->getData());
    }

    public function updatePortalOpen($id, $isopen = true)
    {
        $id = (int) $id;
        $data['isopen'] = (bool) $isopen;

        return $this->_getDao()->update($id, $data);
    }

    public function deletePortal($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            return false;
        }

        return $this->_getDao()->delete($id);
    }

    public function batchDelete($ids)
    {
        if (!is_array($ids) || !$ids) {
            return false;
        }

        return $this->_getDao()->batchDelete($ids);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignPortalDao');
    }
}
