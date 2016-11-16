<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 *
 * 帖子删除扩展服务接口--删除帖子投票
 *
 * @author Mingqu Luo<luo.mingqu@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package wind
 */

class PwDeleteTopicDoPollDelete extends iPwGleanDoHookProcess
{
    public $record = array();

    public function gleanData($value)
    {
        if ($value['special'] == 1) {
            $this->record[] = $value['tid'];
        }
    }

    public function run($ids)
    {
        $threadPollDs = Wekit::load('poll.PwThreadPoll'); /* @var $threadPollDs PwThreadPoll */

        $pollThread = $threadPollDs->fetchPoll($this->record);
        if (!$pollThread) {
            return false;
        }

        $service = Wekit::load('poll.srv.PwPollService'); /* @var $service PwPollService */
        foreach ($pollThread as $value) {
            $service->deletePoll($value['poll_id']);
        }

        $threadPollDs->batchDeletePoll($this->record);

        Wind::import('SRV:forum.dm.PwTopicDm');
        $dm = new PwTopicDm(true);
        $dm->setSpecial(0);
        Wekit::load('forum.PwThread')->batchUpdateThread($this->record, $dm, PwThread::FETCH_MAIN);

        return true;
    }
}
