<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 合并版块--移动帖子.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUniteForumDoMoveActicle.php 21318 2012-12-04 09:24:09Z jieyin $
 */
class PwUniteForumDoMoveActicle extends iPwDoHookProcess
{
    public function run($ids)
    {
        $so = new PwThreadSo();
        $so->setFid($this->srv->fid);
        $data = Wekit::load('forum.PwThread')->searchThread($so, 0);
        $tids = array_keys($data);

        $dm = new PwTopicDm();
        $dm->setFid($this->srv->tofid);
        Wekit::load('forum.PwThread')->batchUpdateThread($tids, $dm, PwThread::FETCH_MAIN);

        $dm = new PwReplyDm();
        $dm->setFid($this->srv->tofid);
        Wekit::load('forum.PwThread')->batchUpdatePostByTid($tids, $dm);
    }
}
