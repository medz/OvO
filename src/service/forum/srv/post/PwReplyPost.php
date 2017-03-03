<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 回复发布相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReplyPost.php 28950 2013-05-31 05:58:25Z jieyin $
 */
class PwReplyPost extends PwPostAction
{
    public $tid;
    protected $pid;
    protected $info;

    public function __construct($tid, PwUserBo $user = null)
    {
        $this->tid = $tid;
        $this->info = $this->_getThreadsService()->getThread($tid);
        parent::__construct($this->info['fid'], $user);
    }

    /**
     * @see PwPostAction.isInit
     */
    public function isInit()
    {
        return !empty($this->info);
    }

    /**
     * @see PwPostAction.check
     */
    public function check()
    {
        if (($result = $this->forum->allowReply($this->user)) !== true) {
            return new PwError('BBS:forum.permissions.reply.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if (!$this->forum->foruminfo['allow_reply'] && !$this->user->getPermission('allow_reply')) {
            return new PwError('permission.reply.allow', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if (Pw::getstatus($this->info['tpcstatus'], PwThread::STATUS_LOCKED) && !$this->user->getPermission('reply_locked_threads')) {
            return new PwError('permission.reply.fail.locked', array('{grouptitle}' => $this->user->getGroupInfo('name')));
        }
        if ($this->forum->forumset['locktime'] && ($this->info['created_time'] + $this->forum->forumset['locktime'] * 86400) < Pw::getTime()) {
            return new PwError('BBS:forum.thread.locked.not');
        }
        if (($result = $this->checkPostNum()) !== true) {
            return $result;
        }
        if (($result = $this->checkPostPertime()) !== true) {
            return $result;
        }

        return true;
    }

    /**
     * @see PwPostAction.getDm
     */
    public function getDm()
    {
        return new PwReplyDm(0, $this->forum, $this->user);
    }

    /**
     * @see PwPostAction.getInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @see PwPostAction.getAttachs
     */
    public function getAttachs()
    {
        return array();
    }

    /**
     * @see PwPostAction.dataProcessing
     */
    public function dataProcessing(PwPostDm $postDm)
    {
        $postDm->setTid($this->tid)
            ->setFid($this->forum->fid)
            ->setAuthor($this->user->uid, $this->user->username, $this->user->ip)
            ->setCreatedTime(Pw::getTime())
            ->setDisabled($this->isDisabled());

        if (($result = $this->checkContentHash($postDm->getContent())) !== true) {
            return $result;
        }
        if (($postDm = $this->runWithFilters('dataProcessing', $postDm)) instanceof PwError) {
            return $postDm;
        }
        $this->postDm = $postDm;

        return true;
    }

    /**
     * @see PwPostAction.execute
     */
    public function execute()
    {
        $result = $this->_getThreadsService()->addPost($this->postDm);
        if ($result instanceof PwError) {
            return $result;
        }
        $this->pid = $result;
        $this->afterPost();

        return true;
    }

    /**
     * 回帖后续操作<更新版块、缓存等信息>.
     */
    public function afterPost()
    {
        if ($rpid = $this->postDm->getField('rpid')) {
            Wekit::load('forum.PwPostsReply')->add($this->pid, $rpid);
        }
        if ($this->postDm->getIscheck()) {
            $title = $this->postDm->getTitle() ? $this->postDm->getTitle() : 'Re:'.$this->info['subject'];
            $this->forum->addPost($this->tid, $this->user->username, $title);

            $dm = new PwTopicDm($this->tid);
            $timestamp = Pw::getTime();
            if ($this->info['lastpost_time'] > $timestamp || Pw::getstatus($this->info['tpcstatus'], PwThread::STATUS_DOWNED)) {
                $timestamp = null;
            }
            $dm->addReplies(1)->addHits(1)->setLastpost($this->user->uid, $this->user->username, $timestamp);
            $this->_getThreadsService()->updateThread($dm, PwThread::FETCH_MAIN);

            if ($rpid) {
                $dm = new PwReplyDm($rpid);
                $dm->addReplies(1);
                $this->_getThreadsService()->updatePost($dm);
            }
        }
    }

    /**
     * @see PwPostAction.afterRun
     */
    public function afterRun()
    {
        $this->runDo('addPost', $this->pid, $this->tid);
    }

    public function getCreditOperate()
    {
        return 'post_reply';
    }

    public function isForumContentCheck()
    {
        return intval($this->forum->forumset['contentcheck']) & 2;
    }

    public function updateUser()
    {
        $userDm = parent::updateUser();
        $userDm->addPostnum(1)->addTodaypost(1)->setPostcheck($this->getHash($this->postDm->getContent()));

        return $userDm;
    }

    public function getNewId()
    {
        return $this->pid;
    }

    /**
     * Enter description here ...
     *
     * @return PwNoticeService
     */
    protected function _getNoticeService()
    {
        return Wekit::load('message.srv.PwNoticeService');
    }

    protected function _getThreadsService()
    {
        return Wekit::load('forum.PwThread');
    }
}
