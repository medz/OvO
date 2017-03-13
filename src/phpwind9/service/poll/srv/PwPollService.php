<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 投票service服务层
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com> 2012-01-21
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwPollService.php 2552 2012-01-12 11:28:21Z mingxing.sun $
 */
class PwPollService
{
    /**
     * 用户投票.
     *
     * @param int   $userid
     * @param int   $pollid
     * @param array $option
     *
     * @return bool || PwError
     */
    public function doVote($userid, $pollid, $option)
    {
        $userid = intval($userid);
        $pollid = intval($pollid);
        if (!$userid || !$pollid) {
            return new PwError('VOTE:fail');
        }

        if (empty($option) || !is_array($option)) {
            return new PwError('VOTE:not.select.option');
        }

        $poll = $this->_getPollDs()->getPoll($pollid);
        if (!$poll) {
            return new PwError('VOTE:thread.not.exist');
        }

        $isVoted = $this->_getPollVoterDs()->isVoted($userid, $pollid);
        if ($isVoted) {
            return new PwError('VOTE:is.voted');
        }

        $voteTimeslimit = $poll['option_limit'] ? $poll['option_limit'] : 1;

        if (count($option) > $voteTimeslimit) {
            return new PwError('VOTE:most.times.limit', ['{mosttimes}' => $voteTimeslimit]);
        }

        foreach ($option as $optionid) {
            $this->_getPollVoterDs()->add($userid, $pollid, $optionid);

            $pollOptionDm = new PwPollOptionDm($optionid);
            $pollOptionDm->addVotedNum(1);
            $this->_getPollOptionDs()->update($pollOptionDm);
        }

        //更新该投票人数
        $voterNum = $this->_getPollVoterDs()->countUser($pollid);

        $pollDm = new PwPollDm($pollid); /* @var $pollDm PwPollDm */
        $pollDm->setVoterNum($voterNum);

        $this->_getPollDs()->updatePoll($pollDm);

        return true;
    }

    /**
     * 删除投票.
     *
     * @param unknown_type $pollid
     *
     * @return bool
     */
    public function deletePoll($pollid)
    {
        $pollid = intval($pollid);

        $poll = $this->getPoll($pollid);
        if (!$poll) {
            return false;
        }

        $this->_getPollDs()->deletePoll($pollid);
        $poll['isinclude_img'] && $this->_removePollImg($pollid);
        $this->_getPollOptionDs()->deleteByPollid($pollid);
        $this->_getPollVoterDs()->deleteByPollid($pollid);

        return true;
    }

    /**
     * 重置选项投票数.
     *
     * @param int $optionid
     */
    public function resetOptionVotedNum($optionid)
    {
        if (!$optionid) {
            return false;
        }

        $optionInfo = $this->_getPollOptionDs()->get($optionid);
        $votedNum = intval($optionInfo['voted_num']);
        $total = intval($this->_getPollVoterDs()->countUserByOptionid($optionid));

        if ($votedNum == $total) {
            return true;
        }

        $dm = new PwPollOptionDm($optionid); /* @var $dm PwPollOptionDm */
        $dm->setVotedNum($total);

        return $this->_getPollOptionDs()->update($dm);
    }

    /**
     * 获取单条投票记录.
     *
     * @param int $pollid 投票记录ID
     *
     * @return array
     */
    public function getPoll($pollid)
    {
        return $this->_getPollDs()->getPoll($pollid);
    }

    private function _removePollImg($pollid)
    {
        $pollid = intval($pollid);
        if (!$pollid) {
            return false;
        }

        $option = $this->_getPollOptionDs()->getByPollid($pollid);
        $images = [];
        foreach ($option as $value) {
            $images[] = $value['image'];
        }

        if (!$images) {
            return false;
        }
        //$attachDs = Wekit::load('attach.PwAttach'); /* @var $attachDs PwAttach */

        foreach ($images as $value) {
            $this->removeImg($value);
        }

        return true;
    }

    public function removeImg($path, $ifthumb = 1)
    {
        Pw::deleteAttach($path, $ifthumb);
    }

    /**
     * get PwPoll.
     *
     * @return PwPoll
     */
    protected function _getPollDs()
    {
        return Wekit::load('poll.PwPoll');
    }

    /**
     * get PwPollOption.
     *
     * @return PwPollOption
     */
    protected function _getPollOptionDs()
    {
        return Wekit::load('poll.PwPollOption');
    }

    /**
     * get PwPollVoter.
     *
     * @return PwPollVoter
     */
    protected function _getPollVoterDs()
    {
        return Wekit::load('poll.PwPollVoter');
    }

    /**
     * get PwThreadPoll.
     *
     * @return PwThreadPoll
     */
    protected function _getThreadPollDs()
    {
        return Wekit::load('poll.PwThreadPoll');
    }
}
