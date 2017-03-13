<?php


/**
 * 帖子管理操作-置顶.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDoTopped.php 24747 2013-02-20 03:13:43Z jieyin $
 */
class PwThreadManageDoTopped extends PwThreadManageDo
{
    public $topped = 0;
    public $overtime = 0;
    public $fids = [];

    protected $tids = [];
    protected $overids = [];

    /* (non-PHPdoc)
     * @see PwThreadManageDo::check()
     */
    public function check($permission)
    {
        if (!isset($permission['topped']) || !$permission['topped']) {
            return false;
        }
        if (array_diff(Pw::collectByKey($this->srv->data, 'topped'), ['0'])) {
            $log = Wekit::load('log.PwLog')->fetchLogByTid(array_keys($this->srv->data), ['19', '20', '21']);
            if (!$this->srv->user->comparePermission(Pw::collectByKey($log, 'created_userid'))) {
                return new PwError('permission.level.topped', ['{grouptitle}' => $this->srv->user->getGroupInfo('name')]);
            }
        }
        if ($this->topped) {
            if ($this->topped > $permission['topped_type']) {
                return new PwError('BBS:manage.error.topped.permission.'.$this->topped);
            }
        }

        return true;
    }

    /**
     * 设置置顶方式.
     *
     * @param int $topped
     *
     * @return bool
     */
    public function setTopped($topped)
    {
        $topped = intval($topped);
        if ($topped < 1 || $topped > 3) {
            return false;
        }
        $this->topped = $topped;
    }

    public function setFids($fids)
    {
        $this->fids = $fids;
    }

    public function setOvertime($overtime)
    {
        if ($overtime) {
            $this->overtime = is_numeric($overtime) ? $overtime : Pw::str2time($overtime) + 86399;
        }
    }

    public function gleanData($value)
    {
        if ($value['fid'] > 0) {
            $this->tids[$value['fid']][] = $value['tid'];
        }
        if ($this->overtime && (!$value['overtime'] || $value['overtime'] > $this->overtime)) {
            $this->overids[] = $value['tid'];
        }
    }

    public function run()
    {
        $_tids = [];
        $_dms = [];
        $specialSort = $this->_getSpecialSort($this->topped);
        foreach ($this->tids as $fid => $tids) {
            $topicDm = new PwTopicDm(true);
            $topicDm->setTopped($this->topped);
            $topicDm->setSpecialsort($specialSort);

            if ($this->topped == 1) {
                $fids = [$fid];
            } elseif ($this->topped == 2) {
                $fids = $this->_getCateList($fid);
            } elseif ($this->topped == 3) {
                $fids = $this->fids ? $this->fids : array_keys(Wekit::load('forum.srv.PwForumService')->getForumList());
            } else {
                $fids = [];
            }
            foreach ($fids as $_fid) {
                foreach ($tids as $tid) {
                    $dm = new PwThreadSortDm();
                    $dm->setFid($_fid)
                        ->setTid($tid)
                        ->setType('topped')
                        ->setExtra($this->topped)
                        ->setEndtime($this->overtime)
                        ->setCreatedTime(Pw::getTime());
                    $_dms[] = $dm;
                }
            }
            $_tids = array_merge($_tids, $tids);
        }
        Wekit::load('forum.PwThread')->batchUpdateThread($_tids, $topicDm, PwThread::FETCH_MAIN);

        if ($this->overtime) {
            if ($this->overids) {
                $topicDm = new PwTopicDm(true);
                $topicDm->setOvertime($this->overtime);
                Wekit::load('forum.PwThread')->batchUpdateThread($this->overids, $topicDm, PwThread::FETCH_MAIN);
            }
            $this->_getOvertimeDs()->batchAdd($_tids, 'topped', $this->overtime);
        } else {
            $this->_getOvertimeDs()->batchDeleteByTidAndType($_tids, 'topped');
        }

        $sortDs = Wekit::load('forum.PwSpecialSort');
        $sortDs->batchDeleteSpecialSortByTid($_tids);
        $sortDs->batchAdd($_dms);

        $this->_addManageLog();
    }

    public function _getCateList($fid)
    {
        $array = [];
        $list = Wekit::load('forum.srv.PwForumService')->getForumList();
        $pa = $list[$fid];
        if ($list[$fid]['type'] == 'category') {
            $cateid = $fid;
        } else {
            $tmp = explode(',', $list[$fid]['fup']);
            $cateid = array_pop($tmp);
        }
        $array[] = $cateid;
        foreach ($list as $key => $value) {
            if ($value['type'] != 'category' && strpos(','.$value['fup'].',', ','.$cateid.',') !== false) {
                $array[] = $value['fid'];
            }
        }

        return $array;
    }

    private function _getSpecialSort($topped)
    {
        $specialSort = 0;
        if ($topped > 0) {
            $const = "SPECIAL_SORT_TOP$topped";
            eval("\$specialSort = PwThread::$const;");
        }

        return $specialSort;
    }

    protected function _getOvertimeDs()
    {
        return Wekit::load('forum.PwOvertime');
    }

    /**
     * 添加日志的.
     */
    private function _addManageLog()
    {
        $_logDms = [];
        if ($this->topped == 1) {
            $type = 'topped';
        } elseif ($this->topped == 2) {
            $type = 'catetopped';
        } elseif ($this->topped == 3) {
            $type = 'sitetopped';
        } else {
            $type = 'untopped';
        }
        /* @var $logSrv PwLogService */
        $logSrv = Wekit::load('log.srv.PwLogService');

        return $logSrv->addThreadManageLog($this->srv->user, $type, $this->srv->getData(), $this->_reason, $this->overtime ? $this->overtime : '永久');
    }
}
