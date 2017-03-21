<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子编辑相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwTopicModify.php 24888 2013-02-25 08:12:54Z jieyin $
 */
class PwTopicModify extends PwPostAction
{
    public $tid;
    protected $info;

    public function __construct($tid, PwUserBo $user = null)
    {
        $this->tid = $tid;
        $threadService = $this->_getThreadService();
        $this->info = $threadService->getThread($tid, PwThread::FETCH_ALL);
        parent::__construct($this->info['fid'], $user);
    }

    /**
     * @see PwPostAction.isInit
     */
    public function isInit()
    {
        return ! empty($this->info);
    }

    public function getSpecial()
    {
        return $this->info['special'];
    }

    /**
     * @see PwPostAction.check
     */
    public function check()
    {
        if (! $this->user->isExists()) {
            return new PwError('login.not');
        }
        if ($this->info['created_userid'] != $this->user->uid) {
            if (! $this->user->getPermission('operate_thread.edit', $this->isBM)) {
                return new PwError('BBS:post.modify.error.self');
            }
            if (! $this->user->comparePermission($this->info['created_userid'])) {
                return new PwError('permission.level.edit', ['{grouptitle}' => $this->user->getGroupInfo('name')]);
            }
        }
        if ($this->forum->forumset['edittime'] && (Pw::getTime() - $this->info['created_time'] > $this->forum->forumset['edittime'] * 60) && ! $this->user->getPermission('operate_thread.edit', $this->isBM)) {
            return new PwError('BBS:post.modify.timelimit', ['{minute}' => $this->forum->forumset['edittime']]);
        }
        $thread_edit_time = $this->user->getPermission('thread_edit_time');
        if ($thread_edit_time > 0 && (Pw::getTime() - $this->info['created_time']) > $thread_edit_time * 60) {
            return new PwError('permission.thread.modify.timelimit');
        }

        return true;
    }

    /**
     * @see PwPostAction.getDm
     */
    public function getDm()
    {
        return new PwTopicDm($this->tid, $this->forum, $this->user);
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
        $attach = [];
        if ($this->info['aids']) {
            $attach = $this->_getAttachService()->getAttachByTid($this->tid, [0]);
        }

        return $attach;
    }

    /**
     * @see PwPostAction.dataProcessing
     */
    public function dataProcessing(PwPostDm $postDm)
    {
        $_gtime = $this->user->getPermission('post_modify_time');
        $modifyTime = ($_gtime && (Pw::getTime() - $this->info['created_time'] > $_gtime * 60)) ? Pw::getTime() : 0;
        $postDm->setDisabled($this->isDisabled())
            ->setModifyInfo($this->user->uid, $this->user->username, $this->user->ip, $modifyTime);
        if (! Pw::getstatus($this->info['tpcstatus'], PwThread::STATUS_OPERATORLOG) && $this->info['created_userid'] != $this->user->uid) {
            $postDm->setOperatorLog(true);
        }
        if (($result = $this->checkTopictype($postDm)) !== true) {
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
        $result = $this->_getThreadService()->updateThread($this->postDm);
        if ($result instanceof PwError) {
            return $result;
        }
        $this->afterPost();

        return true;
    }

    /**
     * 编辑帖子后续操作<更新版块、缓存等信息>.
     */
    public function afterPost()
    {
        if ($this->postDm->getIscheck() != $this->info['ischeck']) {
            $topic = $this->info['ischeck'] ? -1 : 1;
            Wekit::load('forum.srv.PwForumService')->updateStatistics($this->forum, $topic, 0, $topic);
        }
        //编辑非自己的帖子添加管理日志
        if ($this->info['created_userid'] != $this->user->uid) {
            $thread = $this->info;
            $thread['subject'] = $this->postDm->getField('subject') ? $this->postDm->getField('subject') : $this->info['subject'];
            Wekit::load('log.srv.PwLogService')->addEditThreadLog($this->user, $thread);
        }
    }

    /**
     * @see PwPostAction.afterRun
     */
    public function afterRun()
    {
        $this->runDo('updateThread', $this->tid);
    }

    public function isForumContentCheck()
    {
        return intval($this->forum->forumset['contentcheck']) & 1;
    }

    public function getNewId()
    {
        return $this->tid;
    }

    protected function _getThreadService()
    {
        return Wekit::load('forum.PwThread');
    }

    protected function _getAttachService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
