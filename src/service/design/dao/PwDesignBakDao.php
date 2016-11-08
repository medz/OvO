<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDesignBakDao.php 17646 2012-09-07 07:10:53Z gao.wanggao $
 * @package
 */
class PwDesignBakDao extends PwBaseDao
{
    protected $_table = 'design_bak';
    protected $_dataStruct = array('bak_type', 'page_id', 'is_snapshot', 'bak_info');

    public function getBak($type, $pageId, $issnap)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `bak_type` = ? AND `page_id` = ? AND `is_snapshot` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($type, $pageId, $issnap));
    }

    public function replaceBak($data)
    {
        $data['is_snapshot'] = (int) $data['is_snapshot'];
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        if (!$data['bak_type'] || !$data['page_id']) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function updateSnap($type, $pageid, $snap, $issnap)
    {
        if (!$type || !$pageid) {
            return false;
        }
        $sql = $this->_bindTable('UPDATE %s SET `is_snapshot`= ? WHERE `bak_type` = ? AND `page_id` =? AND `is_snapshot` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($issnap, $type, $pageid, $snap));
    }

    public function deleteBak($type, $pageId, $issnap)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `bak_type` = ? AND `page_id` = ? AND `is_snapshot` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($type, $pageId, $issnap));
    }

    public function deleteByPageId($pageId)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE  `page_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($pageId));
    }
}
