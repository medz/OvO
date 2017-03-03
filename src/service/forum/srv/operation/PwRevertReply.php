<?php

//
//
//
//

/**
 * 回复回收站帖子及其关联操作(扩展).
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwRevertReply.php 13302 2012-07-05 03:45:43Z jieyin $
 */
class PwRevertReply extends PwGleanDoProcess
{
    public $data = array();
    public $pids = array();
    public $user;

    public function __construct($pids, PwUserBo $user)
    {
        $this->data = $this->_initData($pids);
        $this->user = $user;
        parent::__construct();
    }

    protected function _initData($pids)
    {
        $data = Wekit::load('forum.PwThread')->fetchPost($pids);
        $recycle = Wekit::load('recycle.PwReplyRecycle')->fetchRecord($pids);
        $tids = array();
        foreach ($recycle as $key => $value) {
            if (!isset($data[$value['pid']])) {
                continue;
            }
            $data[$value['pid']]['src_tid'] = $value['tid'];
            $tids[] = $value['tid'];
        }
        $thread = Wekit::load('forum.PwThread')->fetchThread($tids);
        foreach ($data as $key => $value) {
            isset($thread[$value['src_tid']]) && $value['src_tid_disabled'] = $thread[$value['src_tid']]['disabled'];
            $data[$key] = $value;
        }

        return $data;
    }

    public function getData()
    {
        return $this->data;
    }

    protected function init()
    {
        $this->appendDo(new PwRevertReplyDoMain($this));
        //$this->appendDo(new PwDeleteReplyDoVirtualDelete($this));
        //$this->appendDo(new PwDeleteReplyDoDirectDelete($this));
        //$this->appendDo(new PwDeleteArticleDoAttachDelete($this));
        //$this->appendDo(new PwDeleteArticleDoForumUpdate($this));
        //$this->appendDo(new PwDeleteReplyDoFreshDelete($this));
    }

    protected function gleanData($value)
    {
        $this->pids[] = $value['pid'];
    }

    public function getIds()
    {
        return $this->pids;
    }

    protected function run()
    {
        return true;
    }
}
