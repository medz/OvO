<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');
Wind::import('SRV:forum.dm.PwTopicDm');

/**
 * 帖子管理操作-加亮
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoHighlight.php 24735 2013-02-19 03:23:38Z jieyin $
 * @package forum
 */

class PwThreadManageDoHighlight extends PwThreadManageDo
{
    public $hightlight;
    public $overtime = 0;

    protected $tids;
    protected $overids = array();

    public function check($permission)
    {
        return (isset($permission['highlight']) && $permission['highlight']) ? true : false;
    }

    public function setHighlight($hightlight)
    {
        $this->hightlight = $hightlight;

        return $this;
    }

    public function setOvertime($overtime)
    {
        if ($overtime) {
            $this->overtime = is_numeric($overtime) ? $overtime : Pw::str2time($overtime) + 86399;
        }

        return $this;
    }

    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
        if ($this->overtime && (!$value['overtime'] || $value['overtime'] > $this->overtime)) {
            $this->overids[] = $value['tid'];
        }
    }

    public function run()
    {
        $topicDm = new PwTopicDm(true);
        $topicDm->setHighlight($this->hightlight);
        $threadDs = Wekit::load('forum.PwThread');
        $threadDs->batchUpdateThread($this->tids, $topicDm, PwThread::FETCH_MAIN);
        if ($this->overtime) {
            if ($this->overids) {
                $topicDm = new PwTopicDm(true);
                $topicDm->setOvertime($this->overtime);
                $threadDs->batchUpdateThread($this->overids, $topicDm, PwThread::FETCH_MAIN);
            }
            $this->_getOvertimeDs()->batchAdd($this->tids, 'highlight', $this->overtime);
        } else {
            $this->_getOvertimeDs()->batchDeleteByTidAndType($this->tids, 'highlight');
        }
        //管理日志添加
        Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'highlight', $this->srv->getData(), $this->_reason, $this->overtime ? $this->overtime : '永久');
    }

    protected function _getOvertimeDs()
    {
        return Wekit::load('forum.PwOvertime');
    }
}
