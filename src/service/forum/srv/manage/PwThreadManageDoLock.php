<?php


/**
 * 帖子管理操作-锁定.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDoLock.php 24736 2013-02-19 09:24:40Z jieyin $
 */
class PwThreadManageDoLock extends PwThreadManageDo
{
    public $locked;

    protected $tids;

    public function check($permission)
    {
        if (!isset($permission['lock']) || !$permission['lock']) {
            return false;
        }
        if (!$this->srv->user->comparePermission(Pw::collectByKey($this->srv->data, 'created_userid'))) {
            return new PwError('permission.level.lock', array('{grouptitle}' => $this->srv->user->getGroupInfo('name')));
        }

        return true;
    }

    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    public function run()
    {
        $dm = new PwTopicDm(true);
        $type = '';
        if ($this->locked == 2) {
            $dm->setClosed(1)->setLocked(0);
            $type = 'closed';
        } elseif ($this->locked == 1) {
            $dm->setClosed(0)->setLocked(1);
            $type = 'lock';
        } else {
            $dm->setClosed(0)->setLocked(0);
            $type = 'unlock';
        }
        Wekit::load('forum.PwThread')->batchUpdateThread($this->tids, $dm, PwThread::FETCH_MAIN);

        Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, $type, $this->srv->getData(), $this->_reason);
    }
}
