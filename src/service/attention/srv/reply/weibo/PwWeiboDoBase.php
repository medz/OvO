<?php

/**
 * 微博扩展.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
abstract class PwWeiboDoBase
{
    public function check($dm)
    {
        return true;
    }

    /**
     * 回复发布成功后调用.
     *
     * @param int $weiboId
     */
    public function addWeibo($weiboId)
    {
    }
}
