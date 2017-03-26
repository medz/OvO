<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteTopicDoDirectDelete.php 13278 2012-07-05 02:08:39Z jieyin $
 */
class PwDeleteTopicDoDirectDelete extends iPwGleanDoHookProcess
{
    protected $recode = [];

    public function gleanData($value)
    {
        if ($value['disabled'] == 2) {
            $this->recode[] = $value['tid'];
        }
    }

    public function run($ids)
    {
        $service = Wekit::load('forum.PwThread');
        $service->batchDeleteThread($ids);
        $service->batchDeletePostByTid($ids);

        if ($this->recode) {
            Wekit::load('recycle.PwTopicRecycle')->batchDelete($this->recode);
        }
    }
}
