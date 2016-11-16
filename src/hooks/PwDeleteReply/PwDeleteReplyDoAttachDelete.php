<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 帖子删除扩展服务接口--虚拟删除到回收站
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoAttachDelete.php 23356 2013-01-09 03:20:51Z jieyin $
 * @package forum
 */

class PwDeleteReplyDoAttachDelete extends iPwGleanDoHookProcess
{
    public $tids = array();

    public function gleanData($value)
    {
        $this->tids[$value['from_tid']] = 1;
    }

    public function run($ids)
    {
        if ($this->srv->isRecycle
            || (!$attachs = Wekit::load('attach.PwThreadAttach')->fetchAttachByTidAndPid(array_keys($this->tids), $ids))) {
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
