<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 根据用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchReplyByTidAndPids.php 11974 2012-06-15 04:03:36Z jieyin $
 * @package forum
 */

class PwFetchReplyByTidAndPids implements iPwDataSource
{
    public $tid;
    public $pids;

    public function __construct($tid, $pids)
    {
        $this->tid = $tid;
        $this->pids = $pids;
    }

    public function getData()
    {
        $result = array();
        if (in_array(0, $this->pids)) {
            $this->pids = array_diff($this->pids, array(0));
            $topic = Wekit::load('forum.PwThread')->getThread($this->tid, PwThread::FETCH_ALL);
            $topic['pid'] = 0;
            $result[] = $topic;
        }
        if ($this->pids) {
            $result = array_merge($result, Wekit::load('forum.PwThread')->fetchPost($this->pids));
        }

        return $result;
    }
}
