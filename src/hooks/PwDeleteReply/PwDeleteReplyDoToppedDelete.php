<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 帖子删除扩展服务接口--删除回帖置顶
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwDeleteReplyDoFreshDelete.php 8959 2012-04-28 09:06:05Z jieyin $
 * @package forum
 */

class PwDeleteReplyDoToppedDelete extends iPwGleanDoHookProcess
{
    protected $recode = array();
    protected $tids = array();

    public function gleanData($value)
    {
        if ($value['topped']) {
            $this->record[] = $value['pid'];
            $this->tids[] = $value['tid'];
        }
    }

    public function run($ids)
    {
        Wekit::load('forum.PwPostsTopped')->batchDeleteTopped($this->record);
        Wind::import('SRV:forum.dm.PwTopicDm');
        $dm = new PwTopicDm(true);
        $dm->addReplyTopped(-1);
        Wekit::load('forum.PwThread')->batchUpdateThread($this->tids, $dm, PwThread::FETCH_MAIN);
    }
}
