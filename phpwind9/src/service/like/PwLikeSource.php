<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>.
 *
 * @author $Author$ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwLikeSource
{
    public function getSource($sid)
    {
        $sid = (int) $sid;
        if ($sid < 1) {
            return [];
        }

        return $this->_getDao()->getSource($sid);
    }

    public function getSourceByAppAndFromid($fromapp, $fromid)
    {
        if (empty($fromapp) && $fromid < 1) {
            return [];
        }

        return $this->_getDao()->getSourceByAppAndFromid($fromapp, $fromid);
    }

    public function fetchSource($ids)
    {
        if (! is_array($ids) || count($ids) < 1) {
            return [];
        }

        return $this->_getDao()->fetchSource($ids);
    }

    public function addSource(PwLikeSourceDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->addSource($dm->getData());
    }

    public function deleteSource($sid)
    {
        $sid = (int) $sid;
        if ($sid < 1) {
            return [];
        }

        return $this->_getDao()->deleteSource($sid);
    }

    public function updateSource(PwLikeSourceDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateSource($dm->sid, $dm->getData());
    }

    private function _getDao()
    {
        return Wekit::loadDao('like.dao.PwLikeSourceDao');
    }
}
