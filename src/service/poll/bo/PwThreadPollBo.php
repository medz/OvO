<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 投票业务模型.
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadPollBo.php 18510 2012-09-19 01:55:21Z jieyin $
 */
class PwThreadPollBo
{
    public $tid;
    public $pollid;

    public $info = array();

    public function __construct($tid)
    {
        $this->tid = $tid;
        $this->_init();
    }

    /**
     * 初始化信息.
     */
    protected function _init()
    {
        $threadInfo = $this->_getForumThreadDs()->getThread($this->tid);
        if (!$threadInfo) {
            return false;
        }

        $pollThread = $this->_getThreadPollDs()->getPoll($this->tid);
        if (!$pollThread) {
            return false;
        }

        $this->pollid = $pollThread['poll_id'];
        $this->info = array_merge($threadInfo, $pollThread, $this->getPollInfo($this->pollid));

        return $this->info;
    }

    /**
     * 检测信息是否初始化.
     *
     * @return bool
     */
    public function isInit()
    {
        return $this->info ? true : false;
    }

    /**
     * 获得投票信息.
     *
     * @param array $data
     */
    public function getPollInfo($pollid)
    {
        if (!$pollid) {
            return array();
        }

        $poll = $this->_getPollDs()->getPoll($pollid);
        $poll['ismultiple'] = $poll['option_limit'] > 1 ? true : false;
        $poll['expiredday'] = $poll['expired_time'] ? ($poll['expired_time'] - $poll['created_time']) / 86400 : 0;

        $options = (array) $this->_getPollOptionDs()->getByPollid($pollid);

        $votedTotal = 0;
        foreach ($options as $key => $value) {
            $options[$key]['image'] = $value['image'] ? $value['image'] : '';
            $votedTotal += $value['voted_num'];
        }

        $poll['optionnum'] = count($options);
        $poll['votedtotal'] = $votedTotal;

        return array('poll' => $poll, 'option' => $options);
    }

    public function getRegtimeLimit()
    {
        return $this->info['poll']['regtime_limit'] ? $this->info['poll']['regtime_limit'] : 0;
    }

    public function isExpired()
    {
        return ($this->info['poll']['expired_time'] && $this->info['poll']['expired_time'] < Pw::getTime()) ? true : false;
    }

    public function check()
    {
        if (($result = $this->checkForumGroup()) !== true) {
            return $result;
        }

        return true;
    }

    public function checkForumGroup()
    {
        $fid = $this->info['fid'];
        if (!$fid) {
            return new PwError('BBS:forum.thread.exists.not');
        }

        $forum = new PwForumBo($fid);
        $user = Wekit::getLoginUser();
        $forum->allowVisit($user);
        if (($result = $forum->allowVisit($user)) !== true) {
            return new PwError('BBS:forum.permissions.visit.allow', array('{grouptitle}' => $user->getGroupInfo('name')));
        }

        if (($result = $forum->allowRead($user)) !== true) {
            return new PwError('BBS:forum.permissions.read.allow', array('{grouptitle}' => $user->getGroupInfo('name')));
        }

        return true;
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
     * get PwThreadPoll.
     *
     * @return PwThreadPoll
     */
    protected function _getThreadPollDs()
    {
        return Wekit::load('poll.PwThreadPoll');
    }

    /**
     * get PwThread.
     *
     * @return PwThread
     */
    protected function _getForumThreadDs()
    {
        return Wekit::load('forum.PwThread');
    }
}
