<?php

/**
 * 帖子基础dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadsBaseDao.php 14442 2012-07-20 09:10:11Z jieyin $
 */
class PwThreadsBaseDao extends PwBaseDao
{
    public function getThread($tid)
    {
        return ['tid' => $tid];
    }

    public function fetchThread($tids)
    {
        $data = [];
        foreach ($tids as $value) {
            $data[$value] = [];
        }

        return $data;
    }

    public function getThreadByFid($fid, $limit, $offset)
    {
        $result = $this->_getDao()->getThreadByFid($fid, $limit, $offset);

        return $this->_clearData($result);
    }

    public function getThreadByUid($uid, $limit, $offset)
    {
        $result = $this->_getDao()->getThreadByUid($uid, $limit, $offset);

        return $this->_clearData($result);
    }

    public function getThreadsByFidAndUids($fid, $uids, $limit, $offset)
    {
        $result = $this->_getDao()->getThreadsByFidAndUids($fid, $uids, $limit, $offset);

        return $this->_clearData($result);
    }

    public function addThread($fields)
    {
        return false;
    }

    public function updateThread($tid, $fields, $increaseFields = [], $bitFields = [])
    {
        return true;
    }

    public function batchUpdateThread($tids, $fields, $increaseFields = [], $bitFields = [])
    {
        return true;
    }

    public function deleteThread($tid)
    {
        return true;
    }

    public function batchDeleteThread($tids)
    {
        return true;
    }

    protected function _clearData($result)
    {
        $data = [];
        foreach ($result as $key => $value) {
            $data[$key] = [];
        }

        return $data;
    }

    protected function _getDao()
    {
        return Wekit::loadDao('forum.dao.PwThreadsDao');
    }
}
