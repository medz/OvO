<?php

defined('WEKIT_VERSION') || exit('Forbidden');


/**
 * 帖子投票关系基础DS服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadPoll.php 17614 2012-09-07 03:14:46Z yanchixia $
 * @package poll
 */

class PwThreadPoll
{
    /**
     * 添加帖子投票关系
     *
     * @param  object $dm
     * @return int
     */
    public function addPoll(PwThreadPollDm $dm)
    {
        if (($result = $dm->beforeAdd()) instanceof PwError) {
            return $result;
        }

        return $this->_getThreadPollDao()->addPoll($dm->getData());
    }

    /**
     * 根据投票ID删除关系信息
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
        $this->_getThreadPollDao()->deleteByPollid($pollid);

        return true;
    }

    /**
     * 删除投票帖
     *
     * @param  int  $tid
     * @return bool
     */
    public function deletePoll($tid)
    {
        $tid = intval($tid);
        if ($tid < 1) {
            return false;
        }

        return $this->_getThreadPollDao()->deletePoll($tid);
    }

    /**
     * 批量删除投票帖
     *
     * @param unknown_type $tids
     */
    public function batchDeletePoll($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return false;
        }

        return $this->_getThreadPollDao()->batchDeletePoll($tids);
    }

    /**
     * 根据tid获取投票关系信息
     *
     * @param  int   $tid
     * @return array
     */
    public function getPoll($tid)
    {
        $tid = intval($tid);
        if ($tid < 1) {
            return array();
        }

        return $this->_getThreadPollDao()->getPoll($tid);
    }

    /**
     * 根据tids获取投票关系信息
     *
     * @param  int   $tids
     * @return array
     */
    public function fetchPoll($tids)
    {
        if (empty($tids) || !is_array($tids)) {
            return array();
        }

        return $this->_getThreadPollDao()->fetchPoll($tids);
    }

    /**
     * 根据pollid获取投票关系内容
     *
     * @param int $pollid 投票ID
     *                    return array
     */
    public function getPollByPollid($pollid)
    {
        $pollid = intval($pollid);
        if ($pollid < 1) {
            return array();
        }

        return $this->_getThreadPollDao()->getPollByPollid($pollid);
    }

    /**
     * 通过帖子ID获取批量关系信息
     *
     * @param  array $pollids
     * @return array
     */
    public function fetchByPollid($pollids)
    {
        if (empty($pollids) || !is_array($pollids)) {
            return array();
        }

        return $this->_getThreadPollDao()->fetchByPollid($pollids);
    }

    /**
     * get PwThreadPollDao
     *
     * @return PwThreadPollDao
     */
    protected function _getThreadPollDao()
    {
        return Wekit::loadDao('poll.dao.PwThreadPollDao');
    }
}
