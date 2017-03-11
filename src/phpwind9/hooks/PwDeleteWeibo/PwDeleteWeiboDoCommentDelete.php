<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 微博删除扩展服务接口--删除微博评论.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteWeiboDoCommentDelete.php 8959 2012-04-28 09:06:05Z jieyin $
 */
class PwDeleteWeiboDoCommentDelete extends iPwGleanDoHookProcess
{
    public $record = array();

    public function gleanData($value)
    {
    }

    public function run($ids)
    {
        Wekit::load('weibo.PwWeibo')->batchDeleteCommentByWeiboId($ids);
    }
}
