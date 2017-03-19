<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子列表相关服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadListDoBase.php 21030 2012-11-26 10:22:03Z jieyin $
 */
abstract class PwThreadListDoBase
{
    public function initData($threaddb)
    {
    }

    public function bulidThread($thread)
    {
        return $thread;
    }

    /**
     * 在这里输出插件内容 (位置：标题后面).
     */
    public function createHtmlAfterSubject($thread)
    {
    }
}
