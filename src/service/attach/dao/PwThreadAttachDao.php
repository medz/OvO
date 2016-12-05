<?php

/**
 * 附件基础dao服务
 *
 * the last known user to change this file in the repository  <$LastChangedBy: jieyin $>
 *
 * @author Jianmin Chen <sky_hold@163.com>
 *
 * @version $Id: PwThreadAttachDao.php 24314 2013-01-28 08:09:53Z jieyin $
 */
class PwThreadAttachDao extends PwBaseDao
{
    protected $_table = 'attachs_thread';
    protected $_pk = 'aid';
    protected $_dataStruct = array('aid', 'fid', 'tid', 'pid', 'name', 'type', 'size', 'hits', 'width', 'height', 'path', 'ifthumb', 'special', 'cost', 'ctype', 'created_userid', 'created_time', 'descrip');

    public function getAttach($aid)
    {
        return $this->_get($aid);
    }

    public function fetchAttach($aids)
    {
        return $this->_fetch($aids);
    }

    public function getAttachByTid($tid, $pids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid=? AND pid IN %s', $this->getTable(), $this->sqlImplode($pids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($tid), 'aid');
    }

    public function getTmpAttachByUserid($userid)
    {
        $sql = $this->_bindTable("SELECT * FROM %s WHERE tid='0' AND pid='0' AND created_userid=?");
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($userid), 'aid');
    }

    public function countType($tid, $pid, $type)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS count FROM %s WHERE tid=? AND pid=? AND type=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($tid, $pid, $type));
    }

    public function fetchAttachByTid($tids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('aid');
    }

    public function fetchAttachByTidAndPid($tids, $pids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s AND pid IN %s', $this->getTable(), $this->sqlImplode($tids), $this->sqlImplode($pids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('aid');
    }

    public function fetchAttachByTidsAndPid($tids, $pid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid IN %s AND pid=?', $this->getTable(), $this->sqlImplode($tids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($pid), 'aid');
    }

    public function addAttach($fields)
    {
        if (!isset($fields['aid']) || !$fields = $this->_filterStruct($fields)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s SET %s', $this->getTable(), $this->sqlSingle($fields));
        $this->getConnection()->execute($sql);

        return true;
    }

    public function updateAttach($aid, $fields, $increaseFields = array())
    {
        return $this->_update($aid, $fields, $increaseFields);
    }

    public function updateFid($fid, $tofid)
    {
        $sql = $this->_bindTable('UPDATE %s SET fid=? WHERE fid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($tofid, $fid));
    }

    public function batchUpdateAttach($aids, $fields, $increaseFields = array())
    {
        return $this->_batchUpdate($aids, $fields, $increaseFields);
    }

    public function batchUpdateFidByTid($tids, $fid)
    {
        $sql = $this->_bindSql('UPDATE %s SET fid=? WHERE tid IN %s', $this->getTable(), $this->sqlImplode($tids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update(array($fid));
    }

    public function deleteAttach($aid)
    {
        return $this->_delete($aid);
    }

    public function batchDeleteAttach($aids)
    {
        return $this->_batchDelete($aids);
    }
}
