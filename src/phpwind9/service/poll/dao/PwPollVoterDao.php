<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 用户投票基础dao服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPollVoteDao.php 5717 2012-03-09 02:31:54Z mingxing.sun $
 */
class PwPollVoterDao extends PwBaseDao
{
    protected $_table = 'app_poll_voter';
    protected $_dataStruct = ['uid', 'poll_id', 'option_id', 'created_time'];

    public function getPollByUid($uid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT DISTINCT(poll_id) FROM %s WHERE uid = ? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid]);
    }

    public function getUserByOptionid($optionid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT DISTINCT(uid) FROM %s WHERE option_id = ?  ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$optionid]);
    }

    public function getPollByUidAndPollid($uid, $pollids)
    {
        $sql = $this->_bindSql('SELECT DISTINCT(poll_id) FROM %s WHERE uid = ? AND poll_id IN %s', $this->getTable(), $this->sqlImplode($pollids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$uid]);
    }

    public function getByPollid($pollid)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE poll_id = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$pollid]);
    }

    public function fetchPollByUid($uids, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT DISTINCT(poll_id) FROM %s WHERE uid IN %s ORDER BY created_time DESC %s', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function fetchByPollid($pollids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE poll_id IN %s', $this->getTable(), $this->sqlImplode($pollids));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function countUser($pollid)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM (SELECT DISTINCT(uid) FROM %s where poll_id = ? ) a ', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$pollid]);
    }

    public function countByOptionid($optionid)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM %s where option_id = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$optionid]);
    }

    public function countUserByOptionid($optionid)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM (SELECT DISTINCT(uid) FROM %s where option_id = ?) a ', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$optionid]);
    }

    public function countByUid($uid)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM (SELECT DISTINCT(poll_id) FROM %s where uid = ?) a ', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue([$uid]);
    }

    public function countByUids($uids)
    {
        $sql = $this->_bindSql('SELECT count(*) FROM (SELECT DISTINCT(poll_id) FROM %s where uid IN %s) a ', $this->getTable(), $this->sqlImplode($uids));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchColumn();
    }

    public function add($fieldData)
    {
        return $this->_add($fieldData);
    }

    public function deleteByPollid($pollid)
    {
        $sql = $this->_bindTable('DELETE FROM %s WHERE poll_id = ?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$pollid]);
    }
}
