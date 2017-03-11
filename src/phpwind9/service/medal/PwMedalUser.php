<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwMedalUser.php 20389 2012-10-29 03:41:38Z gao.wanggao $
 */
class PwMedalUser
{
    /**
     * 获取一条记录.
     *
     * @param int $uid
     */
    public function getMedalUser($uid)
    {
        $uid = (int) $uid;
        if ($uid < 1) {
            return array();
        }

        return $this->_getDao()->getInfo($uid);
    }

    /**
     * 获取多条记录.
     *
     * @param array $uid
     */
    public function fetchMedalUser($uids)
    {
        if (!is_array($uids) || count($uids) < 1) {
            return array();
        }

        return $this->_getDao()->fetchInfo($uids);
    }

    /**
     * 获取指定多个用户勋章的排行.
     *
     * @param array $uids
     * @param int   $start
     * @param int   $limit
     */
    public function fetchMedalUserOrder($uids, $start = 0, $limit = 10)
    {
        if (!is_array($uids) || count($uids) < 1) {
            return array();
        }

        return $this->_getDao()->fetchMedalUserOrder($uids, $start, $limit);
    }

    /**
     * 获取勋章总排行.
     *
     * @param int $limit
     */
    public function getTotalOrder($limit = 10)
    {
        return $this->_getDao()->getTotalOrder($limit);
    }

    /**
     * 获取勋章需要更新的用户，用于计划任务
     * Enter description here ...
     *
     * @param int $expiredTime
     */
    public function getExpiredMedalUser($expiredTime = 0, $start = 0, $limit = 10)
    {
        $expiredTime = (int) $expiredTime;
        if ($expiredTime < 1) {
            return false;
        }

        return $this->_getDao()->getExpiredMedalUser($expiredTime, $start, $limit);
    }

    /**
     * 统计勋章需要更新的用户总数
     * Enter description here ...
     *
     * @param int $expiredTime
     */
    public function countExpiredMedalUser($expiredTime = 0)
    {
        $expiredTime = (int) $expiredTime;
        if ($expiredTime < 1) {
            return 0;
        }

        return $this->_getDao()->countExpiredMedalUser($expiredTime);
    }

    /**
     * 统计总计录数
     * Enter description here ...
     */
    public function countMedalUser()
    {
        return $this->_getDao()->countMedalUser();
    }

    /**
     * 分页获取勋章统计
     * Enter description here ...
     *
     * @param int $start
     * @param int $perpage
     */
    public function getMedalUserList($start = 0, $perpage = 10)
    {
        $start = (int) $start;
        $perpage = (int) $perpage;

        return $this->_getDao()->getMedalUserList($start, $perpage);
    }

    public function replaceInfo(PwMedalUserDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }
        $data = $dm->getData();
        $data['uid'] = $dm->uid;

        return $this->_getDao()->replaceInfo($data);
    }

    /**
     * 删除勋章总数为零的记录
     * Enter description here ...
     */
    public function deleteMedalUsersByCount()
    {
        return $this->_getDao()->deleteMedalUsersByCount();
    }

    private function _getDao()
    {
        return Wekit::loadDao('medal.dao.PwMedalUserDao');
    }
}
