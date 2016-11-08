<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 投票基本信息dao服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPollDao.php 9051 2012-05-03 01:57:24Z hejin $
 * @package poll
 */

class PwPollDao extends PwBaseDao
{
    protected $_table = 'app_poll';
    protected $_pk = 'poll_id';
    protected $_dataStruct = array('poll_id', 'voter_num', 'isafter_view', 'isinclude_img', 'option_limit', 'regtime_limit', 'created_userid', 'app_type', 'expired_time', 'created_time');

    public function getPoll($pollId)
    {
        return $this->_get($pollId);
    }

    public function getPollList($limit, $offset, $orderby)
    {
        $orderby = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT * FROM %s %s %s ', $this->getTable(), $orderby, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll($this->_pk);
    }

    public function getPollByTime($startTime, $endTime, $limit, $offset, $orderby)
    {
        $orderby = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_time > ? AND created_time < ? %s %s ', $this->getTable(), $orderby, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($startTime, $endTime));
    }

    public function getPollByUid($uid, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid = ? ORDER BY created_time DESC %s', $this->getTable(), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($uid));
    }

    public function fetchPoll($pollids)
    {
        return $this->_fetch($pollids);
    }

    public function fetchPollByPollid($pollids, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE poll_id IN %s ORDER BY created_time DESC %s', $this->getTable(), $this->sqlImplode($pollids), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function fetchPollByUid($uids, $limit, $offset)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE created_userid IN %s ORDER BY created_time DESC %s', $this->getTable(), $this->sqlImplode($uids), $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchAll();
    }

    public function countPollByTime($startTime = 0, $endTime = 0)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s WHERE created_time > ? AND created_time < ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($startTime, $endTime));
    }

    public function countPollByUid($uid)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) as count FROM %s WHERE created_userid = ?', $this->getTable());
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue(array($uid));
    }

    public function countPollByUids($uids)
    {
        $sql = $this->_bindSql('SELECT COUNT(*) as count FROM %s WHERE created_userid IN %s', $this->getTable(), $this->sqlImplode($uids));
        $smt = $this->getConnection()->query($sql);

        return $smt->fetchColumn();
    }

    public function addPoll($fieldData)
    {
        return $this->_add($fieldData);
    }

    public function deletePoll($pollId)
    {
        return $this->_delete($pollId);
    }

    public function updatePoll($pollid, $fieldData)
    {
        return $this->_update($pollid, $fieldData);
    }

    private function _buildOrderby($orderby)
    {
        $array = array();
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'voter_num':
                    $array[] = 'voter_num '.($value ? 'ASC' : 'DESC');
                    break;
                case 'created_time':
                    $array[] = 'created_time '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }
}
