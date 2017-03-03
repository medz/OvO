<?php

defined('WEKIT_VERSION') || exit('Forbidden');

 

/**
 * 帖子内容页回复列表数据接口 / 只看某用户列表|只看楼主.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUserRead.php 16856 2012-08-29 04:27:45Z jieyin $
 */
class PwUserRead extends PwReadDataSource
{
    public $thread;
    public $info;
    public $tid;
    public $uid;

    public function __construct(PwThreadBo $thread, $uid)
    {
        $this->thread = $thread;
        $this->tid = $thread->tid;
        $this->uid = $uid;
        $this->info = &$thread->info;
        $this->urlArgs['uid'] = $uid;
    }

    public function initPage($total)
    {
        $this->maxpage = ceil($total / $this->perpage);
        $this->page < 1 && $this->page = 1;
        $this->page > $this->maxpage && $this->page = $this->maxpage;
    }

    public function execute()
    {
        $this->total = Wekit::load('forum.PwThread')->countPostByTidAndUid($this->tid, $this->uid) + 1;
        $this->initPage($this->total);

        list($start, $limit) = Pw::page2limit($this->page, $this->perpage);
        if ($start == 0) {
            $this->info['pid'] = 0;
            $this->data[] = &$this->info;    //地址引用，便于bulidRead同步修改
            $this->info['aids'] && $this->_aids[] = 0;
            $this->_uids[] = $this->info['created_userid'];
        }
        if ($this->total > 1) {
            $offset = $start;
            $offset == 0 ? $limit-- : $offset--;
            $replies = Wekit::load('forum.PwThread')->getPostByTidAndUid($this->tid, $this->uid, $limit, $offset, $this->asc);
            foreach ($replies as $value) {
                $this->data[] = $value;
                $value['aids'] && $this->_aids[] = $value['pid'];
                $this->_uids[] = $value['created_userid'];
            }
        }
        $this->firstFloor = $start;
    }
}
