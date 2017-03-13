<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 主题回收站记录.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwTopicRecycle.php 14354 2012-07-19 10:36:06Z jieyin $
 */
class PwTopicRecycle
{
    /**
     * 获取多条回收站记录.
     *
     * @param array $tids
     *
     * @return array
     */
    public function fetchRecord($tids)
    {
        if (!$tids || !is_array($tids)) {
            return [];
        }

        return $this->_getDao()->fetchRecord($tids);
    }

    /**
     * 添加一条回收站记录(帖子).
     *
     * @param PwTopicRecycleDm $dm
     *
     * @return bool
     */
    public function add(PwTopicRecycleDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao()->add($dm->getData());
    }

    /**
     * 批量添加回收站记录(帖子).
     *
     * @param array $dms PwTopicRecycleDm的对象集合
     *
     * @return bool
     */
    public function batchAdd($dms)
    {
        if (empty($dms) || !is_array($dms)) {
            return false;
        }
        $data = [];
        foreach ($dms as $key => $dm) {
            if ($dm instanceof PwTopicRecycleDm && $dm->beforeAdd() === true) {
                $data[] = $dm->getData();
            }
        }
        if (!$data) {
            return false;
        }

        return $this->_getDao()->batchAdd($data);
    }

    /**
     * 批量删除回收站记录.
     *
     * @param array $tids
     *
     * @return bool
     */
    public function batchDelete($tids)
    {
        if (empty($tids)) {
            return false;
        }

        return $this->_getDao()->batchDelete($tids);
    }

    /**
     * 统计回收站帖子数(搜索).
     *
     * @param object $so
     *
     * @return int
     */
    public function countSearchRecord(PwRecycleThreadSo $so)
    {
        return $this->_getDao()->countSearchRecord($so->getData());
    }

    /**
     * 搜索回收站的帖子.
     *
     * @param array $param
     *
     * @return array
     */
    public function searchRecord(PwRecycleThreadSo $so, $limit = 20, $offset = 0)
    {
        return $this->_getDao()->searchRecord($so->getData(), $so->getOrderby(), $limit, $offset);
    }

    protected function _getDao()
    {
        return Wekit::loadDao('recycle.dao.PwTopicRecycleDao');
    }
}
