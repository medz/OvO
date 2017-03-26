<?php

/**
 * 帖子购买记录 / dao服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadsBuyDao.php 24066 2013-01-21 07:30:33Z jinlong.panjl $
 */
class PwThreadsBuyDao extends PwBaseDao
{
    protected $_table = 'bbs_threads_buy';
    protected $_dataStruct = ['tid', 'pid', 'created_userid', 'created_time', 'ctype', 'cost'];

    public function sumCost($tid, $pid)
    {
        $sql = $this->_bindTable('SELECT SUM(cost) AS sum FROM %s WHERE tid=? AND pid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$tid, $pid]);
    }

    public function get($tid, $pid, $uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tid=? AND pid=? AND created_userid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne([$tid, $pid, $uid]);
    }

    public function countByTidAndPid($tid, $pid)
    {
        $sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE tid=? AND pid=? ');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$tid, $pid]);
    }

    public function getByTidAndPid($tid, $pid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE tid=? AND pid=? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$tid, $pid], 'created_userid');
    }

    public function getByTidAndUid($tid, $uid)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE tid=? AND created_userid=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$tid, $uid], 'pid');
    }

    public function add($fields)
    {
        return $this->_add($fields, false);
    }
}
