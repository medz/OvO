<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.do.PwThreadDisplayDoBase');

/**
 * 投票展示.
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadDisplayDoPoll.php 19692 2012-10-17 05:16:40Z jieyin $
 */
class PwThreadDisplayDoPoll extends PwThreadDisplayDoBase
{
    public $user = null;
    public $info;

    public $isVoted;
    public $isAllowView;
    public $isViewVoter;

    public $isAllowVote;

    public function __construct($tid, PwUserBo $user)
    {
        $this->info = $this->_getThreadPollBo($tid)->info;
        $this->user = $user;

        $this->isVoted = $this->isVoted();
        $this->isAllowView = $this->isAllowView();
        $this->isViewVoter = $this->isAllowViewVoter();
        $this->isAllowVote = $this->isAllowVote();
    }

    public function isVoted()
    {
        return $this->_getPwPollVoterDs()->isVoted($this->user->uid, $this->info['poll_id']);
    }

    public function isAllowView()
    {
        if (!$this->info['poll']['isafter_view'] || $this->isVoted) {
            return true;
        }

        return false;
    }

    public function isAllowViewVoter()
    {
        if ($this->user->getPermission('allow_view_vote')) {
            return true;
        }

        return false;
    }

    public function isAllowVote()
    {
        if ((!$this->info['poll']['expired_time'] || ($this->info['poll']['expired_time'] && $this->info['poll']['expired_time'] > Pw::getTime())) && $this->user->getPermission('allow_participate_vote')) {
            return true;
        }

        return false;
    }

    public function createHtmlBeforeContent($read)
    {
        if ($read['pid'] == 0) {
            PwHook::template('displayVoteHtml', 'TPL:bbs.read_vote', true, $this);
        }
    }

    public function createHtmlAfterContent($read)
    {
        if ($read['pid'] == 0) {
            PwHook::template('displayVoteHtmlAfterContent', 'TPL:bbs.read_vote', true, $this);
        }
    }

    /**
     * get getThreadPollBo.
     *
     * @return PwThreadPollBo
     */
    private function _getThreadPollBo($tid)
    {
        Wind::import('SRV:poll.bo.PwThreadPollBo');

        return new PwThreadPollBo($tid);
    }

    /**
     * get PwPollVoter.
     *
     * @return PwPollVoter
     */
    private function _getPwPollVoterDs()
    {
        return Wekit::load('poll.PwPollVoter');
    }
}
