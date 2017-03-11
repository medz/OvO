<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子删除扩展服务接口--虚拟删除到回收站.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwDeleteReplyDoPostUpdate.php 15530 2012-08-07 10:45:08Z jieyin $
 */
class PwDeleteReplyDoPostUpdate extends iPwGleanDoHookProcess
{
    public $record = array();

    public function gleanData($value)
    {
        if ($value['disabled'] == 0 && $value['rpid']) {
            $this->record[$value['rpid']]++;
        }
    }

    public function run($ids)
    {
        $srv = Wekit::load('forum.PwThread');
        foreach ($this->record as $rpid => $value) {
            $dm = new PwReplyDm($rpid);
            $dm->addReplies(-$value);
            $srv->updatePost($dm);
        }
    }
}
