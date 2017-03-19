<?php

/**
 * 帖子索引dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadsCateIndexDao.php 22309 2012-12-21 07:58:00Z jieyin $
 */
class PwThreadsCateIndexDao extends PwBaseDao
{
    protected $_table = 'bbs_threads_cate_index';
    protected $_pk = 'tid';
    protected $_dataStruct = ['tid', 'cid', 'fid', 'disabled', 'created_time', 'lastpost_time'];
    protected $_threadTable = 'bbs_threads';

    public function count($cid)
    {
        $sql = $this->_bindTable('SELECT count(*) as count FROM %s WHERE cid=? AND disabled=0');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$cid]);
    }

    public function countNotInFids($cid, $fids)
    {
        $sql = $this->_bindSql('SELECT count(*) as count FROM %s WHERE cid=? AND disabled=0 AND fid NOT IN %s', $this->getTable(), $this->sqlImplode($fids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$cid]);
    }

    public function fetch($cid, $limit, $offset, $order)
    {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE cid=? AND disabled=0 ORDER BY %s DESC %s', $this->getTable(), $idx, $field, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$cid], 'tid');
    }

    public function fetchNotInFid($cid, $fids, $limit, $offset, $order)
    {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE cid=? AND disabled=0 AND fid NOT IN %s ORDER BY %s DESC %s', $this->getTable(), $idx, $this->sqlImplode($fids), $field, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$cid], 'tid');
    }

    /*
    public function fetchNotInFid($fids, $limit, $offset, $order) {
        list($field, $idx) = $this->_getOrderFieldAndIndex($order);
        $sql = $this->_bindSql('SELECT * FROM %s FORCE INDEX(%s) WHERE fid NOT IN %s AND disabled=0 ORDER BY %s DESC %s', $this->getTable(), $idx, $this->sqlImplode($fids), $field, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);
        return $smt->fetchAll('tid');
    }

    public function countThreadNotInFids($fids) {
        $sql = $this->_bindSql('SELECT count(*) as count FROM %s WHERE fid NOT IN %s AND disabled=0', $this->getTable(), $this->sqlImplode($fids));
        $smt = $this->getConnection()->query($sql);
        return $smt->fetchColumn();
    }*/

    protected function _getOrderFieldAndIndex($order)
    {
        if ($order == 'postdate') {
            return ['tid', 'PRIMARY'];
        }

        return ['lastpost_time', 'idx_cid_lastposttime'];
    }

    public function addThread($tid, $fields)
    {
        $fields['tid'] = $tid;
        $fields = $this->_processField($fields);

        return $this->_add($fields, false);
    }

    public function updateThread($tid, $fields, $increaseFields = [])
    {
        $fields = $this->_processField($fields);

        return $this->_update($tid, $fields, $increaseFields);
    }

    public function batchUpdateThread($tids, $fields, $increaseFields = [])
    {
        $fields = $this->_processField($fields);

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

    public function deleteOver($cid, $limit)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE cid=? ORDER BY lastpost_time ASC LIMIT %s', $this->getTable(), intval($limit));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$cid]);
    }

    private function _processField($fields)
    {
        if (isset($fields['fid'])) {
            $fields['cid'] = $fields['fid'] ? Wekit::load('forum.srv.PwForumService')->getCateId($fields['fid']) : 0;
        }

        return $fields;
    }
}
