<?php

Wind::import('SRC:library.base.PwBaseDao');
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDesignSegmentDao.php 13231 2012-07-04 05:07:41Z gao.wanggao $
 */
class PwDesignSegmentDao extends PwBaseDao
{
    protected $_table = 'design_segment';
    protected $_dataStruct = array('segment', 'page_id', 'segment_tpl', 'segment_struct');

    public function getSegment($segment, $pageid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `segment` = ? AND `page_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($segment, $pageid));
    }

    public function getSegmentByPageid($pageid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s  WHERE `page_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($pageid), 'segment');
    }

    public function replaceSegment($data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        if (!$data['segment'] || !$data['page_id']) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s SET %s', $this->getTable(), $this->sqlSingle($data));

        return $this->getConnection()->execute($sql);
    }

    public function deleteSegment($segment, $pageid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE `segment` = ? AND `page_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($segment, $pageid));
    }

    public function deleteSegmentByPageid($pageid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE  `page_id` = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($pageid));
    }
}
