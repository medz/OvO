<?php

/**
 * 前台管理日志DS服务
 *
 * @author xiaoxia.xu<xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: PwLog.php 24747 2013-02-20 03:13:43Z jieyin $
 */
class PwLog
{
    /**
     * 通过帖子ID获取该该帖子的操作记录.
     *
     * @param int $tid
     * @param int $pid   帖子回复ID
     * @param int $limit
     * @param int $start
     *
     * @return array
     */
    public function getLogBytid($tid, $pid, $limit = 10, $start = 0)
    {
        if (0 >= ($tid = intval($tid))) {
            return [];
        }

        return $this->_getLogDao()->getLogByTid($tid, $pid, $limit, $start);
    }

    public function fetchLogByTid($tids, $typeid)
    {
        if (empty($tids) || !is_array($tids) || empty($typeid) || !is_array($typeid)) {
            return [];
        }

        return $this->_getLogDao()->fetchLogByTid($tids, $typeid);
    }

    /**
     * 搜索日志.
     *
     * @param PwLogSo $so
     *
     * @return array
     */
    public function search(PwLogSo $so, $limit, $offset)
    {
        return $this->_getLogDao()->search($so->getCondition(), $limit, $offset);
    }

    /**
     * 根据条件统计日志.
     *
     * @param PwLogSo $so
     *
     * @return int
     */
    public function coutSearch(PwLogSo $so)
    {
        return $this->_getLogDao()->countSearch($so->getCondition());
    }

    /**
     * 添加日志.
     *
     * @param PwLogDm $dm
     *
     * @return int
     */
    public function addLog(PwLogDm $dm)
    {
        if (true !== ($r = $dm->beforeAdd())) {
            return $r;
        }

        return $this->_getLogDao()->addLog($dm->getData());
    }

    /**
     * 批量添加日志.
     *
     * @param array $dms
     *
     * @return bool
     */
    public function batchAddLog($dms)
    {
        if (empty($dms)) {
            return true;
        }
        $datas = [];
        foreach ($dms as $_dm) {
            if (!$_dm instanceof PwLogDm) {
                return false;
            }
            if (true !== ($r = $_dm->beforeAdd())) {
                return $r;
            }
            $data[] = $_dm->getData();
        }

        return $this->_getLogDao()->batchAddLog($data);
    }

    /**
     * 根据日志ID删除日志.
     *
     * @param int $id
     *
     * @return int
     */
    public function deleteLog($id)
    {
        if (0 >= ($id = intval($id))) {
            return false;
        }

        return $this->_getLogDao()->deleteLog($id);
    }

    /**
     * 根据日志ID列表批量删除日志.
     *
     * @param array $logids
     *
     * @return int
     */
    public function batchDeleteLog($logids)
    {
        if (empty($logids)) {
            return false;
        }

        return $this->_getLogDao()->batchDeleteLog($logids);
    }

    /**
     * 清除某一个时间点之前的记录.
     *
     * @param string $time
     *
     * @return bool
     */
    public function clearLogBeforeDatetime($time)
    {
        if (!$time) {
            return false;
        }

        return $this->_getLogDao()->clearLogBeforeDatetime($time);
    }

    /**
     * @return PwLogDao
     */
    protected function _getLogDao()
    {
        return Wekit::loadDao('log.dao.PwLogDao');
    }
}
