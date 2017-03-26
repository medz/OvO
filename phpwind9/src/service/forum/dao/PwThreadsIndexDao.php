<?php

/**
 * 帖子索引dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadsIndexDao.php 22309 2012-12-21 07:58:00Z jieyin $
 */
class PwThreadsIndexDao extends PwBaseDao
{
    protected $_table = 'bbs_threads_index';
    protected $_pk = 'tid';
    protected $_dataStruct = ['tid', 'fid', 'disabled', 'created_time', 'lastpost_time'];
    protected $_threadTable = 'bbs_threads';

    public function count()
    {
        $sql = $this->_bindTable('SELECT count(*) as count FROM %s WHERE disabled=0');
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchColumn();
    }

    public function countThreadInFids($fids)
    {
        $sql = $this->_bindSql('SELECT count(*) as count FROM %s WHERE fid IN %s AND disabled=0', $this->getTable(), $this->sqlImplode($fids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchColumn();
    }

    public function countThreadNotInFids($fids)
    {
        $sql = $this->_bindSql('SELECT count(*) as count FROM %s WHERE fid NOT IN %s AND disabled=0', $this->getTable(), $this->sqlImplode($fids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchColumn();
    }

    public function fetch($limit, $offset, $order)
    {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE disabled=0 ORDER BY %s DESC %s', $this->getTable(), $idx, $field, $this->sqlLimit($limit, $offset));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('tid');
    }

    public function fetchInFid($fids, $limit, $offset, $order)
    {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE fid IN %s AND disabled=0 ORDER BY %s DESC %s', $this->getTable(), $idx, $this->sqlImplode($fids), $field, $this->sqlLimit($limit, $offset));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('tid');
    }

    public function fetchNotInFid($fids, $limit, $offset, $order)
    {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE fid NOT IN %s AND disabled=0 ORDER BY %s DESC %s', $this->getTable(), $idx, $this->sqlImplode($fids), $field, $this->sqlLimit($limit, $offset));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('tid');
    }

    protected function _getOrderFieldAndIndex($order)
    {
        if ($order == 'lastpost') {
            return ['lastpost_time', 'idx_lastposttime'];
        }

        return ['tid', 'PRIMARY'];
    }

    public function addThread($tid, $fields)
    {
        $fields['tid'] = $tid;

        return $this->_add($fields, false);
    }

    public function updateThread($tid, $fields, $increaseFields = [])
    {
        return $this->_update($tid, $fields, $increaseFields);
    }

    public function batchUpdateThread($tids, $fields, $increaseFields = [])
    {
        return $this->_batchUpdate($tids, $fields, $increaseFields);
    }

    public function revertTopic($tids)
    {
        $sql = $this->_bindSql('UPDATE %s a LEFT JOIN %s b ON a.tid=b.tid SET a.disabled=b.disabled WHERE a.tid IN %s', $this->getTable(), $this->getTable($this->_threadTable), $this->sqlImplode($tids));

        return $this->getConnection()->execute($sql);
    }

    public function deleteThread($tid)
    {
        return $this->_delete($tid);
    }

    public function batchDeleteThread($tids)
    {
        return $this->_batchDelete($tids);
    }

    public function deleteOver($limit)
    {
        $sql = $this->_bindSql('DELETE FROM %s ORDER BY tid ASC LIMIT %s', $this->getTable(), intval($limit));

        return $this->getConnection()->execute($sql);
    }
}
