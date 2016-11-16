<?php

defined('WEKIT_VERSION') || exit('Forbidden');


Wind::import('SRV:forum.dm.PwTopicDm');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoDirectDelete.php 13278 2012-07-05 02:08:39Z jieyin $
 * @package forum
 */

class PwDeleteReplyDoDirectDelete extends iPwGleanDoHookProcess
{
    protected $recode = array();
    protected $tids = array();

    public function gleanData($value)
    {
        if ($value['disabled'] == 2 && $value['tid'] == 0) {
            $this->recode[] = $value['pid'];
        }
        if ($value['disabled'] == 0) {
            $this->tids[$value['tid']]++;
        }
    }

    public function run($ids)
    {
        $service = Wekit::load('forum.PwThread');
        $service->batchDeletePost($ids);
        foreach ($this->tids as $tid => $value) {
            $post = current($service->getPostByTid($tid, 1, 0, false));
            $dm = new PwTopicDm($tid);
            $dm->addReplies(-$value);
            $dm->setLastpost($post['created_userid'], $post['created_username'], $post['created_time']);
            Wekit::load('forum.PwThread')->updateThread($dm, PwThread::FETCH_MAIN);
        }
        if ($this->recode) {
            Wekit::load('recycle.PwReplyRecycle')->batchDelete($this->recode);
        }
    }
}
