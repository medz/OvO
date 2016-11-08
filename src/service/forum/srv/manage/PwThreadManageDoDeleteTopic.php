<?php

Wind::import('SRV:forum.srv.manage.PwThreadManageDo');

/**
 * 帖子管理操作-删除帖子
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwThreadManageDoDeleteTopic.php 24736 2013-02-19 09:24:40Z jieyin $
 * @package forum
 */

class PwThreadManageDoDeleteTopic extends PwThreadManageDo
{
    protected $tids;
    protected $isDeductCredit = true;

    public function check($permission)
    {
        if (!isset($permission['delete']) || !$permission['delete']) {
            return false;
        }
        if (!$this->srv->user->comparePermission(Pw::collectByKey($this->srv->data, 'created_userid'))) {
            return new PwError('permission.level.delete', array('{grouptitle}' => $this->srv->user->getGroupInfo('name')));
        }

        return true;
    }

    public function gleanData($value)
    {
        $this->tids[] = $value['tid'];
    }

    public function run()
    {
        if ($this->tids) {
            Wind::import('SRV:forum.srv.operation.PwDeleteTopic');
            Wind::import('SRV:forum.srv.dataSource.PwFetchTopicByTid');
            $service2 = new PwDeleteTopic(new PwFetchTopicByTid($this->tids), $this->srv->user);
            $service2->setRecycle(true)
                ->setIsDeductCredit($this->isDeductCredit)
                ->setReason($this->_reason)
                ->execute();

            Wekit::load('log.srv.PwLogService')->addThreadManageLog($this->srv->user, 'delete', $this->srv->getData(), $this->_reason);
        }
    }

    public function setIsDeductCredit($isDeductCredit)
    {
        $this->isDeductCredit = $isDeductCredit;

        return $this;
    }
}
