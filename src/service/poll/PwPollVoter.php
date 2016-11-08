<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:poll.PwPoll');

/**
 * 用户投票基础DS服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPollVoter.php 3576 2012-01-12 11:48:47Z mingxing.sun $
 * @package poll
 */

class PwPollVoter
{
    /**
     * 添加
     *
     * @param  int $userid
     * @param  int $pollid
     * @param  int $optionid
     * @return int
     */
    public function add($userid, $pollid, $optionid)
    {
        $userid = intval($userid);
        $pollid = intval($pollid);
        $optionid = intval($optionid);
        if (!$userid || !$pollid || !$optionid) {
            return false;
        }

        $fieldData = array('uid' => $userid, 'poll_id' => $pollid, 'option_id' => $optionid, 'created_time' => pw::getTime());

        return $this->_getPollVoterDao()->add($fieldData);
    }

    /**
     * 删除
     *
     * @param  int  $pollid
     * @return bool
     */
    public function deleteByPollid($pollid)
    {
        $pollid = intval($pollid);
        if ($pollid < 1) {
            return false;
        }

        return $this->_getPollVoterDao()->deleteByPollid($pollid);
    }

    /**
     * 统计我参与的投票数
     *
     * @param unknown_type $uid
     */
    public function countByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return 0;
        }

        return $this->_getPollVoterDao()->countByUid($uid);
    }

    /**
     * 根据投票id,获得参与者
     *
     * @param unknown_type $pollid
     */
    public function getByPollid($pollid)
    {
        $pollid = intval($pollid);
        if (1 > $pollid) {
            return array();
        }

        return $this->_getPollVoterDao()->getByPollid($pollid);
    }

    /**
     * 获取我参与的投票
     *
     * @param int $uid 用户ID
     * @param $limit
     * @param $offset
     * @return array
     */
    public function getPollByUid($uid, $limit, $offset)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return array();
        }

        return $this->_getPollVoterDao()->getPollByUid($uid, $limit, $offset);
    }

    /**
     * 统计关注的人的投票数
     *
     * @param  array $uids
     * @return int
     */
    public function countByUids($uids)
    {
        if (empty($uids) || !is_array($uids)) {
            return 0;
        }

        return $this->_getPollVoterDao()->countByUids($uids);
    }

    /**
     * 关注的人的投票
     *
     * @param  array $uids
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function fetchPollByUid($uids, $limit, $offset)
    {
        if (empty($uids) || !is_array($uids)) {
            return array();
        }

        return $this->_getPollVoterDao()->fetchPollByUid($uids, $limit, $offset);
    }

    /**
     * 统计某个选项参与人员数
     *
     * @param  int $pollid
     * @param  int $optionid
     * @return int
     */
    public function countUserByOptionid($optionid)
    {
        $pollid = intval($pollid);
        if (1 > $optionid) {
            return 0;
        }

        return $this->_getPollVoterDao()->countUserByOptionid($optionid);
    }

    /**
     * 获取某个选项参与人员
     *
     * @param  int   $pollid   投票ID
     * @param  int   $optionid 投票项ID
     * @param  int   $limit
     * @param  int   $offset
     * @return array
     */
    public function getUserByOptionid($optionid, $limit = 20, $offset = 0)
    {
        $optionid = intval($optionid);
        if (1 > $optionid) {
            return array();
        }

        return $this->_getPollVoterDao()->getUserByOptionid($optionid, $limit, $offset);
    }

    /**
     * 在指定投票范围内查找用户投票
     *
     * @param unknown_type $pollids
     */
    public function getPollByUidAndPollid($userid, $pollids)
    {
        $userid = intval($userid);
        if (!$userid || empty($pollids) || !is_array($pollids)) {
            return array();
        }

        $poll = $this->_getPollVoterDao()->getPollByUidAndPollid($userid, $pollids);
        if (!$poll) {
            return array();
        }

        $result = array();
        foreach ($poll as $value) {
            $result[] = $value['poll_id'];
        }

        return $result;
    }

    /**
     * 该用户是否投过票
     *
     * @param  int  $userid
     * @param  int  $pollid
     * @return bool
     */
    public function isVoted($userid, $pollid)
    {
        $userid = intval($userid);
        if (!$userid) {
            return false;
        }
        $pollid = is_array($pollid) ? $pollid : array($pollid);
        $poll = $this->_getPollVoterDao()->getPollByUidAndPollid($userid, $pollid);

        return $poll ? true : false;
    }

    /**
     * 统计某个投票人数
     *
     * @param  int $pollid
     * @return int
     */
    public function countUser($pollid)
    {
        $pollid = intval($pollid);
        if (1 > $pollid) {
            return 0;
        }

        return $this->_getPollVoterDao()->countUser($pollid);
    }

    /**
     * 统计某个选项参与次数
     *
     * @param  int $optionid
     * @return int
     */
    public function countByOptionid($optionid)
    {
        $optionid = intval($optionid);
        if (1 > $optionid) {
            return 0;
        }

        return $this->_getPollVoterDao()->countByOptionid($optionid);
    }

    /**
     * get PwPollVoterDao
     *
     * @return PwPollVoterDao
     */
    protected function _getPollVoterDao()
    {
        return Wekit::loadDao('poll.dao.PwPollVoterDao');
    }
}
