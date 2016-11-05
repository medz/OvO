<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:process.iPwGleanDoHookProcess');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwRevertTopicDoMain.php 13302 2012-07-05 03:45:43Z jieyin $
 * @package forum
 */

class PwRevertTopicDoMain extends iPwGleanDoHookProcess
{
    public $fids = array();
    public $tids = array();

    public function gleanData($value)
    {
        if ($value['disabled'] != 2) {
            return;
        }
        $fid = $value['fid'];
        isset($this->fids[$fid]) || $this->fids[$fid] = array('topic' => 0, 'replies' => 0);
        $this->fids[$fid]['replies'] += $value['replies'];
        $value['ischeck'] && $this->fids[$fid]['topic']++;
        $this->tids[] = $value['tid'];
    }

    public function run($ids)
    {
        $threadDs = Wekit::load('forum.PwThread');
        $forumSrv = Wekit::load('forum.srv.PwForumService');

        foreach ($this->fids as $fid => $value) {
            $forumSrv->updateStatistics($fid, $value['topic'], $value['replies']);
        }
        $threadDs->revertTopic($this->tids);
        $threadDs->revertPost($this->tids);

        Wekit::load('recycle.PwTopicRecycle')->batchDelete($ids);
    }
}
