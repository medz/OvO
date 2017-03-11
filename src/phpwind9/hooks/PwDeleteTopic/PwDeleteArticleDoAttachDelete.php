<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteArticleDoAttachDelete.php 23334 2013-01-08 10:22:13Z jieyin $
 */
class PwDeleteArticleDoAttachDelete extends iPwGleanDoHookProcess
{
    public function run($ids)
    {
        if ($this->srv->isRecycle || (!$attachs = Wekit::load('attach.PwThreadAttach')->fetchAttachByTid($ids))) {
            return;
        }

        $aids = array();
        foreach ($attachs as $key => $value) {
            Pw::deleteAttach($value['path'], $value['ifthumb']);
            $aids[] = $key;
        }
        Wekit::load('attach.PwThreadAttach')->batchDeleteAttach($aids);
    }
}
