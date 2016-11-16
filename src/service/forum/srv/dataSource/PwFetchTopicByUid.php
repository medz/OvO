<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 根据用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwFetchTopicByUid.php 14598 2012-07-24 09:44:29Z jieyin $
 * @package forum
 */

class PwFetchTopicByUid implements iPwDataSource
{
    public $uids;

    public function __construct($uids)
    {
        $this->uids = $uids;
    }

    public function getData()
    {
        return Wekit::load('forum.PwThreadExpand')->fetchThreadByUid($this->uids, 0, 0, PwThread::FETCH_ALL);
    }
}
