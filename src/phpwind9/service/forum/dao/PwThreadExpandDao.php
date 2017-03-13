<?php

/**
 * 帖子dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadExpandDao.php 22350 2012-12-21 10:06:31Z jieyin $
 */
class PwThreadExpandDao extends PwBaseDao
{
    protected $_table = 'bbs_threads';
    protected $_pk = 'tid';
    protected $_dataStruct = ['tid', 'fid', 'topic_type', 'subject', 'topped', 'locked', 'digest', 'overtime', 'highlight', 'ischeck', 'replies', 'hits', 'special', 'created_time', 'created_username', 'created_userid', 'created_ip', 'modified_time', 'modified_username', 'modified_userid', 'modified_ip', 'lastpost_time', 'lastpost_userid', 'lastpost_username', 'reply_notice', 'special_sort'];

    public function getThreadByFidOverTime($fid, $lastpostTime, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE fid=? AND disabled=0 AND lastpost_time>? ORDER BY lastpost_time ASC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$fid, $lastpostTime], 'tid');
    }

    public function getThreadByFidUnderTime($fid, $lastpostTime, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE fid=? AND disabled=0 AND lastpost_time<? ORDER BY lastpost_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$fid, $lastpostTime], 'tid');
    }

    public function fetchThreadByUid($uids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid IN %s', $this->getTable(), $this->sqlImplode($uids));
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('tid');
    }

    public function countUserThreadByFidAndTime($fid, $time, $limit)
    {
        $sql = $this->_bindSql('SELECT created_userid,COUNT(*) AS count FROM %s WHERE disabled=0 AND created_time>? AND fid=? GROUP BY created_userid ORDER BY count DESC %s', $this->getTable(), $this->sqlLimit($limit));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$time, $fid], 'created_userid');
    }

    public function countThreadsByFid()
    {
        $sql = $this->_bindTable('SELECT fid,COUNT(*) AS sum FROM %s WHERE disabled=0 GROUP BY fid');
        $rst = $this->getConnection()->query($sql);

        return $rst->fetchAll('fid');
    }

    /**
     * 根据uid统计审核和未审核的帖子.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countDisabledThreadByUid($uid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) AS sum FROM %s WHERE created_userid=? AND disabled < 2');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid]);
    }

    /**
     * 根据uid获取审核和未审核的帖子.
     *
     * @param int $uid
     * @param int $limit
     * @param int $offset
     *
     * @return int
     */
    public function getDisabledThreadByUid($uid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid=? AND disabled < 2 ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid], 'tid');
    }
}
