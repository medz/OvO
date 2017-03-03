<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwRevertReplyDoMain.php 15530 2012-08-07 10:45:08Z jieyin $
 */
class PwRevertReplyDoMain extends iPwGleanDoHookProcess
{
    public $fids = array();
    public $tids = array();
    public $rpids = array();

    public function gleanData($value)
    {
        if ($value['disabled'] != 2 || !$value['src_tid']) {
            return;
        }
        $tid = $value['src_tid'];
        isset($this->tids[$tid]) || $this->tids[$tid] = array('replies' => 0, 'ids' => array(), 'disabled' => $value['src_tid_disabled']);
        $this->tids[$tid]['ids'][] = $value['pid'];
        $this->tids[$tid]['replies']++;
        if ($value['src_tid_disabled'] != 2) {
            $this->fids[$value['fid']]++;
        }
        if ($value['rpid']) {
            $this->rpids[$value['rpid']]++;
        }
    }

    public function run($ids)
    {
         
         

        $threadDs = Wekit::load('forum.PwThread');
        $forumSrv = Wekit::load('forum.srv.PwForumService');

        foreach ($this->tids as $tid => $value) {
            $dm1 = new PwReplyDm(true);
            $dm1->setTid($tid);
            $value['disabled'] != 2 && $dm1->setDisabled(0);
            $threadDs->batchUpdatePost($value['ids'], $dm1);

            $post = current(Wekit::load('forum.PwThread')->getPostByTid($tid, 1, 0, false));
            $dm = new PwTopicDm($tid);
            $dm->addReplies($value['replies']);
            $post && $dm->setLastpost($post['created_userid'], $post['created_username'], $post['created_time']);
            $threadDs->updateThread($dm, PwThread::FETCH_MAIN);
        }

        foreach ($this->fids as $fid => $value) {
            $forumSrv->updateStatistics($fid, 0, $value);
        }

        foreach ($this->rpids as $rpid => $value) {
            $dm = new PwReplyDm($rpid);
            $dm->addReplies($value);
            $threadDs->updatePost($dm);
        }

        Wekit::load('recycle.PwReplyRecycle')->batchDelete($ids);
    }
}
