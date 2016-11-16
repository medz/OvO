<?php

defined('WEKIT_VERSION') || exit('Forbidden');



/**
 * 帖子通过审核及其关联操作(扩展)
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwPassTopic.php 13302 2012-07-05 03:45:43Z jieyin $
 * @package forum
 */

class PwPassTopic extends PwGleanDoProcess
{
    public $data = array();
    public $tids = array();
    public $fids = array();

    public function __construct(iPwDataSource $ds)
    {
        $this->data = $ds->getData();
        parent::__construct();
    }

    public function getData()
    {
        return $this->data;
    }

    protected function gleanData($value)
    {
        if ($value['disabled'] == 1) {
            $this->tids[] = $value['tid'];
            $this->fids[$value['fid']]++;
        }
    }

    public function getIds()
    {
        return $this->tids;
    }

    protected function run()
    {
        Wind::import('SRV:forum.dm.PwTopicDm');
        $dm = new PwTopicDm(true);
        $dm->setDisabled(0);
        Wekit::load('forum.PwThread')->batchUpdateThread($this->tids, $dm, PwThread::FETCH_MAIN);

        foreach ($this->fids as $fid => $value) {
            Wekit::load('forum.srv.PwForumService')->updateStatistics($fid, $value, 0, $value);
        }

        return true;
    }
}
