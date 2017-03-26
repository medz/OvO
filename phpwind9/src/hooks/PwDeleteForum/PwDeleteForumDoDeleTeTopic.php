<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteForumDoDeleTeTopic.php 5735 2012-03-09 05:06:39Z xiaoxia.xuxx $
 */
class PwDeleteForumDoDeleTeTopic extends iPwDoHookProcess
{
    public function run($ids)
    {
        $srv = new PwDeleteTopic(new PwFetchTopicByFid($ids), $this->srv->user);
        $srv->setRecycle(false)->execute();
    }
}
