<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('SRV:forum.srv.threadDisplay.PwReadDataSource');

/**
 * 帖子内容页回复列表数据接口 / 普通列表.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwCommonRead.php 17895 2012-09-10 07:40:24Z jieyin $
 */
class PwCommonRead extends PwReadDataSource
{
    public $thread;
    public $info;

    public function __construct(PwThreadBo $thread)
    {
        $this->thread = $thread;
        $this->info = &$thread->info;
    }

    public function initPage($total)
    {
        $this->maxpage = ceil($total / $this->perpage);
        if ($this->page == 'e') {
            $this->page = $this->maxpage;
        } else {
            $this->page < 1 && $this->page = 1;
            $this->page > $this->maxpage && $this->page = $this->maxpage;
        }
    }

    public function execute()
    {
        $this->total = $this->thread->info['replies'] + $this->thread->info['reply_topped'] + 1;
        $this->initPage($this->total);

        list($start, $limit) = Pw::page2limit($this->page, $this->perpage);
        if ($start == 0) {
            $this->info['pid'] = 0;
            $this->data[] = &$this->info;    //地址引用，便于bulidRead同步修改
            $this->info['aids'] && $this->_aids[] = 0;
            $this->_uids[] = $this->info['created_userid'];
        }
        if ($this->info['replies'] > 0) {
            $offset = $start;
            $offset == 0 ? $limit-- : $offset--;
            $replies = array();
            if ($this->thread->info['reply_topped']) {
                if ($offset < $this->thread->info['reply_topped']) {
                    $replies = $this->_getToppedReply($limit, $offset);
                    $limit -= count($replies);
                    $offset = 0;
                } else {
                    $offset -= $this->thread->info['reply_topped'];
                }
            }
            if ($limit > 0) {
                $replies = array_merge($replies, $this->thread->getReplies($limit, $offset, $this->asc));
            }
            foreach ($replies as $value) {
                $this->data[] = $value;
                $value['aids'] && $this->_aids[] = $value['pid'];
                $this->_uids[] = $value['created_userid'];
            }
        }
        $this->firstFloor = $start;
    }

    /**
     * 帖内置顶.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array()
     */
    private function _getToppedReply($limit, $offset)
    {
        if (!$posts = Wekit::load('forum.PwPostsTopped')->getByTid($this->info['tid'], $limit, $offset)) {
            return array();
        }
        $replies = Wekit::load('forum.PwThread')->fetchPost(array_keys($posts));
        $array = array();
        foreach ($posts as $k => $v) {
            $replies[$k]['istopped'] = 1;
            $replies[$k]['topped_time'] = $v['created_time'];
            $replies[$k]['topped_userid'] = $v['created_userid'];
            $array[] = $replies[$k];
        }

        return $array;
    }
}
