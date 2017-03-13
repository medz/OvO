<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignBak.php 17555 2012-09-06 09:43:13Z gao.wanggao $
 */
class PwDesignBak
{
    const PAGE = 1;
    const SEGMENT = 2;
    const STRUCTURE = 3;
    const MODULE = 4;
    const DATA = 5;

    public function getBak($type, $pageid, $issnap = 0)
    {
        if (!$type || !$pageid) {
            return [];
        }
        $info = $this->_getDao()->getBak($type, $pageid, $issnap);
        if (isset($info['bak_info'])) {
            $info['bak_info'] = unserialize($info['bak_info']);
        }

        return $info;
    }

    /**
     * Enter description here ...
     *
     * @param int        $type
     * @param string|int $pk
     * @param array      $info
     */
    public function replaceBak($type, $pageid, $issnap, $info)
    {
        $data = [];
        if (!$type || !$pageid) {
            return false;
        }
        $data['bak_type'] = $type;
        $data['page_id'] = $pageid;
        $data['is_snapshot'] = $issnap;
        $data['bak_info'] = serialize($info);

        return $this->_getDao()->replaceBak($data);
    }

    public function updateSnap($type, $pageid, $snap = 0, $issnap = 0)
    {
        if (!$type || !$pageid) {
            return false;
        }

        return $this->_getDao()->updateSnap($type, $pageid, $snap, $issnap);
    }

    public function deleteBak($type, $pageid, $issnap = 0)
    {
        if (!$type || !$pageid) {
            return false;
        }

        return $this->_getDao()->deleteBak($type, $pageid, $issnap);
    }

    public function deleteByPageId($pageid)
    {
        if (!$pageid) {
            return false;
        }

        return $this->_getDao()->deleteByPageId($pageid);
    }

    private function _getDao()
    {
        return Wekit::loadDao('design.dao.PwDesignBakDao');
    }
}
