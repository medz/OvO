<?php


/**
 * 帖子管理操作-删除回复.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadManageDoDeleteReply.php 24736 2013-02-19 09:24:40Z jieyin $
 */
class PwThreadManageDoDeleteReply extends PwThreadManageDo
{
    protected $tids;
    protected $pids;
    protected $isDeductCredit = true;

    public function check($permission)
    {
        if (! isset($permission['delete']) || ! $permission['delete']) {
            return false;
        }
        if (! $this->srv->user->comparePermission(Pw::collectByKey($this->srv->data, 'created_userid'))) {
            return new PwError('permission.level.delete', ['{grouptitle}' => $this->srv->user->getGroupInfo('name')]);
        }

        return true;
    }

    public function gleanData($value)
    {
        if ($value['pid']) {
            $this->pids[] = $value['pid'];
        } else {
            $this->tids[] = $value['tid'];
        }
    }

    public function run()
    {
        if ($this->pids) {
            $service1 = new PwDeleteReply(new PwFetchReplyByPid($this->pids), $this->srv->user);
            $service1->setRecycle(true)
                ->setIsDeductCredit($this->isDeductCredit)
                ->setReason($this->_reason)
                ->execute();
            //删除帖子回复
            Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'delete', $service1->data, $this->_reason, '', true);
        }
        if ($this->tids) {
            $service2 = new PwDeleteTopic(new PwFetchTopicByTid($this->tids), $this->srv->user);
            $service2->setRecycle(true)
                ->setIsDeductCredit($this->isDeductCredit)
                ->setReason($this->_reason)
                ->execute();
            //删除帖子
            Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'delete', $service2->data, $this->_reason);
        }
    }

    public function setIsDeductCredit($isDeductCredit)
    {
        $this->isDeductCredit = $isDeductCredit;

        return $this;
    }
}
