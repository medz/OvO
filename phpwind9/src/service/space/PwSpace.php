<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwSpace.php 6270 2012-03-20 02:09:11Z gao.wanggao $
 */
class PwSpace
{
    /**
     * 获取一条记录.
     *
     * @param int $uid
     */
    public function getSpace($uid)
    {
        $uid = (int) $uid;
        if ($uid < 1) {
            return [];
        }

        return $this->_getDao()->getSpace($uid);
    }

    /**
     * 获取多条记录.
     *
     * @param array $uids
     */
    public function fetchSpace($uids)
    {
        if (! is_array($uids) || count($uids) < 1) {
            return [];
        }

        return $this->_getDao()->fetchSpace($uids);
    }

    public function getSpaceByDomain($domian)
    {
        if (empty($domian)) {
            return false;
        }

        return $this->_getDao()->getSpaceByDomain($domian);
    }

    public function addInfo($dm)
    {
        if (! $dm instanceof PwSpaceDm) {
            return new PwError('SPACE:info.error');
        }
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }
        $data = $dm->getData();
        $data['uid'] = $dm->uid;

        return $this->_getDao()->addInfo($data);
    }

    public function updateInfo($dm)
    {
        if (! $dm instanceof PwSpaceDm) {
            return new PwError('SPACE:info.error');
        }
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateInfo($dm->uid, $dm->getData());
    }

    public function updateNumber($uid)
    {
        $uid = (int) $uid;

        return $this->_getDao()->updateNumber($uid);
    }

    public function deleteInfo($uid)
    {
        $uid = (int) $uid;
        if ($uid < 1) {
            return false;
        }

        return $this->_getDao()->deleteInfo($uid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('space.dao.PwSpaceDao');
    }
}
