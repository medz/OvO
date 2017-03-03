<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 
 

/**
 * 帖子编辑相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReplyModify.php 28950 2013-05-31 05:58:25Z jieyin $
 */
class PwReplyModify extends PwPostAction
{
    public $tid;
    protected $pid;
    protected $info;

    public function __construct($pid, PwUserBo $user = null)
    {
        $this->info = $this->_getThreadService()->getPost($pid);
        $this->pid = $pid;
        $this->tid = $this->info['tid'];
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
        if (!$this->user->isExists()) {
            return new PwError('login.not');
        }
        if ($this->info['created_userid'] != $this->user->uid) {
            if (!$this->user->getPermission('operate_thread.edit', $this->isBM)) {
                return new PwError('BBS:post.modify.error.self');
            }
            if (!$this->user->comparePermission($this->info['created_userid'])) {
                return new PwError('permission.level.edit', array('{grouptitle}' => $this->user->getGroupInfo('name')));
            }
        }
        if ($this->forum->forumset['edittime'] && (Pw::getTime() - $this->info['created_time'] > $this->forum->forumset['edittime'] * 60) && !$this->user->getPermission('operate_thread.edit', $this->isBM)) {
            return new PwError('BBS:post.modify.timelimit', array('{minute}' => $this->forum->forumset['edittime']));
        }
        $thread_edit_time = $this->user->getPermission('thread_edit_time');
        if ($thread_edit_time > 0 && (Pw::getTime() - $this->info['created_time']) > $thread_edit_time * 60) {
            return new PwError('permission.thread.modify.timelimit');
        }

        return true;
    }

    /**
     * @see PwPostAction.getInfo
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @see PwPostAction.getDm
     */
    public function getDm()
    {
        return new PwReplyDm($this->pid, $this->forum, $this->user);
    }

    /**
     * @see PwPostAction.getAttachs
     */
    public function getAttachs()
    {
        $attach = array();
        if ($this->info['aids']) {
            $attach = $this->_getAttachService()->getAttachByTid($this->tid, array($this->pid));
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
        $result = $this->_getThreadService()->updatePost($this->postDm);
        if ($result instanceof PwError) {
            return $result;
        }
        $this->afterPost();

        return true;
    }

    /**
     * 编辑回复后续操作<更新版块、缓存等信息>.
     */
    public function afterPost()
    {
        if ($this->postDm->getIscheck() != $this->info['ischeck']) {
            $reply = $this->info['ischeck'] ? -1 : 1;
            Wekit::load('forum.srv.PwForumService')->updateStatistics($this->forum, 0, $reply, $reply);

             
            $dm = new PwTopicDm($this->tid);
            $dm->addReplies($reply);
            $this->_getThreadService()->updateThread($dm, PwThread::FETCH_MAIN);

            if ($this->info['rpid']) {
                $dm = new PwReplyDm($this->info['rpid']);
                $dm->addReplies($reply);
                $this->_getThreadService()->updatePost($dm);
            }
        }
        //编辑非自己的帖子回复添加管理日志

        if ($this->info['created_userid'] != $this->user->uid) {
            $thread = $this->info;
            $thread['subject'] = $this->postDm->getField('subject') ? $this->postDm->getField('subject') : $this->info['subject'];
            Wekit::load('log.srv.PwLogService')->addEditThreadLog($this->user, $thread, true);
        }
    }

    /**
     * @see PwPostAction.afterRun
     */
    public function afterRun()
    {
        $this->runDo('updatePost', $this->pid, $this->tid);
    }

    public function isForumContentCheck()
    {
        return intval($this->forum->forumset['contentcheck']) & 2;
    }

    public function getNewId()
    {
        return $this->pid;
    }

    public function _getThreadService()
    {
        return Wekit::load('forum.PwThread');
    }

    public function _getAttachService()
    {
        return Wekit::load('attach.PwThreadAttach');
    }
}
