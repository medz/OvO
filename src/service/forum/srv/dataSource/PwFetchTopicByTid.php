<?php

defined('WEKIT_VERSION') || exit('Forbidden');

Wind::import('LIB:dataSource.iPwDataSource');

/**
 * 获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchTopicByTid.php 13224 2012-07-04 02:11:45Z jieyin $
 * @package forum
 */

class PwFetchTopicByTid implements iPwDataSource
{
    public $tids;

    public function __construct($tids)
    {
        $this->tids = $tids;
    }

    public function getData()
    {
        return Wekit::load('forum.PwThread')->fetchThread($this->tids, PwThread::FETCH_ALL);
    }
}
