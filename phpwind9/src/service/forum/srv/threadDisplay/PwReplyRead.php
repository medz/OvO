<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子内容页回复列表数据接口 / 普通列表.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwReplyRead.php 10923 2012-05-31 10:23:15Z jieyin $
 */
class PwReplyRead extends PwReadDataSource
{
    public $tid;
    public $pid;

    public function __construct($tid, $pid)
    {
        $this->tid = $tid;
        $this->pid = $pid;
    }

    public function execute()
    {
        $value = Wekit::load('forum.PwThread')->getPost($this->pid);
        $this->data[] = $value;
        $value['aids'] && $this->_aids[] = $value['pid'];
        $this->_uids[] = $value['created_userid'];
        $this->firstFloor = Wekit::load('forum.PwThread')->countPostByTidUnderPid($this->tid, $this->pid) + 1;
    }
}
