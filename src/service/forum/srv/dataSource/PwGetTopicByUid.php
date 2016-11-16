<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 根据用户获取帖子列表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @license http://www.phpwind.com
 * @version $Id: PwGetTopicByUid.php 14598 2012-07-24 09:44:29Z jieyin $
 * @package forum
 */

class PwGetTopicByUid implements iPwDataSource
{
    public $uid;

    public function __construct($uid)
    {
        $this->uid = $uid;
    }

    public function getData()
    {
        return Wekit::load('forum.PwThread')->getThreadByUid($this->uid, 0, 0, PwThread::FETCH_ALL);
    }
}
