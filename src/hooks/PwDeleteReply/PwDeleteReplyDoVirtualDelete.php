<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteReplyDoVirtualDelete.php 14354 2012-07-19 10:36:06Z jieyin $
 */
class PwDeleteReplyDoVirtualDelete extends iPwGleanDoHookProcess
{
    protected $record = array();
    protected $tids = array();

    public function gleanData($value)
    {
        $dm = new PwReplyRecycleDm();
        $dm->setPid($value['pid'])
            ->setTid($value['tid'])
            ->setFid($value['fid'])
            ->setOperateTime(Pw::getTime())
            ->setOperateUsername($this->srv->user->username)
            ->setReason($this->srv->reason);
        $this->record[] = $dm;

        if ($value['disabled'] == 0) {
            $this->tids[$value['tid']]++;
        }
    }

    public function run($ids)
    {
        $service = Wekit::load('forum.PwThread');
        $dm = new PwReplyDm();
        $dm->setDisabled(2)->setTid(0);
        $service->batchUpdatePost($ids, $dm);

        foreach ($this->tids as $tid => $value) {
            $post = current($service->getPostByTid($tid, 1, 0, false));
            $dm = new PwTopicDm($tid);
            $dm->addReplies(-$value);
            $dm->setLastpost($post['created_userid'], $post['created_username'], $post['created_time']);
            Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_MAIN);
        }
        Wekit::load('recycle.PwReplyRecycle')->batchAdd($this->record);
    }
}
