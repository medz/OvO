<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 投票记录接口服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwPoll.php 9437 2012-05-07 06:37:33Z hejin $
 */
class PwPoll
{
    /**
     * 获取单条投票记录.
     *
     * @param int $pollid
     *
     * @return array
     */
    public function getPoll($pollid)
    {
        $pollid = (int) $pollid;
        if ($pollid < 1) {
            return [];
        }

        return $this->_getPollDao()->getPoll($pollid);
    }

    /**
     * 获取投票信息.
     *
     * @param array $pollids
     *
     * @return array
     */
    public function fetchPoll($pollids)
    {
        if (empty($pollids) || !is_array($pollids)) {
            return [];
        }

        return $this->_getPollDao()->fetchPoll($pollids);
    }

    /**
     * 获得最新投票列表.
     *
     * @param int   $limit
     * @param int   $offset
     * @param array $orderby
     */
    public function getPollList($limit, $offset, $orderby = [])
    {
        return $this->_getPollDao()->getPollList($limit, $offset, $orderby);
    }

    /**
     * 统计某个时间段的投票数.
     *
     * @param int $startTime
     * @param int $endTime
     *
     * @return int
     */
    public function countPollByTime($startTime = 0, $endTime = 0)
    {
        return $this->_getPollDao()->countPollByTime($startTime, $endTime);
    }

    /**
     * 获得某个时间段的投票.
     *
     * @param int $startTime
     * @param int $endTime
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getPollByTime($startTime, $endTime, $limit, $offset, $orderby = [])
    {
        return $this->_getPollDao()->getPollByTime($startTime, $endTime, $limit, $offset, $orderby);
    }

    /**
     * 根据 uid 统计投票总数.
     *
     * @param array $uid
     *
     * @return int
     */
    public function countPollByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return 0;
        }

        return $this->_getPollDao()->countPollByUid($uid);
    }

    /**
     * 获取用户的投票列表.
     *
     * @param int $uid
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getPollByUid($uid, $limit, $offset)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return [];
        }

        return $this->_getPollDao()->getPollByUid($uid, $limit, $offset);
    }

    /**
     * 根据 uids 统计投票总数.
     *
     * @param array $uids
     *
     * @return int
     */
    public function countPollByUids($uids)
    {
        if (empty($uids) || !is_array($uids)) {
            return 0;
        }

        return $this->_getPollDao()->countPollByUids($uids);
    }

    /**
     * 获取投票信息.
     *
     * @param array $uids
     * @param int   $offset
     * @param int   $limit
     *
     * @return array
     */
    public function fetchPollByUid($uids, $limit, $offset)
    {
        if (empty($uids) || !is_array($uids)) {
            return [];
        }

        return $this->_getPollDao()->fetchPollByUid($uids, $limit, $offset);
    }

    /**
     * 根据投票IDS 获得投票列表.
     *
     * @param int $pollid
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function fetchPollByPollid($pollids, $limit, $offset)
    {
        if (empty($pollids) || !is_array($pollids)) {
            return [];
        }

        return $this->_getPollDao()->fetchPollByPollid($pollids, $limit, $offset);
    }

    /**
     * 添加投票.
     *
     * @param PwPollDm $dm
     *
     * @return int
     */
    public function addPoll(PwPollDm $dm)
    {
        if (($result = $dm->beforeAdd()) instanceof PwError) {
            return $result;
        }

        return $this->_getPollDao()->addPoll($dm->getData());
    }

    /**
     * 删除投票.
     *
     * @param int $pollid
     *
     * @return bool
     */
    public function deletePoll($pollid)
    {
        $pollid = intval($pollid);
        if ($pollid < 1) {
            return false;
        }

        return $this->_getPollDao()->deletePoll($pollid);
    }

    /**
     * 更新投票选项.
     *
     * @param PwPollDm $dm
     *
     * @return bool
     */
    public function updatePoll(PwPollDm $dm)
    {
        if (($result = $dm->beforeUpdate()) instanceof PwError) {
            return $result;
        }

        return $this->_getPollDao()->updatePoll($dm->poll_id, $dm->getData());
    }

    /**
     * 获取投票基本表 DAO层
     *
     * @return PwPollDao
     */
    protected function _getPollDao()
    {
        return Wekit::loadDao('poll.dao.PwPollDao');
    }
}
