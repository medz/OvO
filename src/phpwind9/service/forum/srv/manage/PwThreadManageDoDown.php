<?php


/**
 * 帖子管理操作-压帖.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDoDown.php 24736 2013-02-19 09:24:40Z jieyin $
 */
class PwThreadManageDoDown extends PwThreadManageDo
{
    public $downtime;
    public $downed;

    protected $tids;

    public function check($permission)
    {
        if (!isset($permission['down']) || !$permission['down']) {
            return false;
        }
        if (!$this->srv->user->comparePermission(Pw::collectByKey($this->srv->data, 'created_userid'))) {
            return new PwError('permission.level.down', array('{grouptitle}' => $this->srv->user->getGroupInfo('name')));
        }

        return true;
    }

    public function setDowntime($time)
    {
        $this->downtime = abs(intval($time)) * 3600;

        return $this;
    }

    public function setDowned($bool)
    {
        $this->downed = $bool;

        return $this;
    }

    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    public function run()
    {
        $dm = new PwTopicDm(true);
        $dm->addLastposttime(-$this->downtime)->setDowned($this->downed);
        Wekit::load('forum.PwThread')->batchUpdateThread($this->tids, $dm, PwThread::FETCH_MAIN);

        Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'down', $this->srv->getData(), $this->_reason, $this->downtime);
    }
}
