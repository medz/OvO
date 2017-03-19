<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子附件基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadAttach.php 24314 2013-01-28 08:09:53Z jieyin $
 */
class PwThreadAttach
{
    /**
     * 获取一个附件信息.
     *
     * @param int $aid
     *
     * @return array
     */
    public function getAttach($aid)
    {
        if (empty($aid)) {
            return [];
        }

        return $this->_getDao()->getAttach($aid);
    }

    /**
     * 获取多个附件信息.
     *
     * @param array $aids
     *
     * @return array
     */
    public function fetchAttach($aids)
    {
        if (empty($aids) || !is_array($aids)) {
            return [];
        }

        return $this->_getDao()->fetchAttach($aids);
    }

    /**
     * 获取帖子(A)中回复序列(B)中的附件信息.
     *
     * @param int   $tid  帖子(A)
     * @param array $pids 回复序列(B)
     *
     * @return array
     */
    public function getAttachByTid($tid, $pids)
    {
        if (!$tid || !$pids) {
            return [];
        }

        return $this->_getDao()->getAttachByTid($tid, (array) $pids);
    }

    /**
     * 获取用户临时存取的附件.
     *
     * @param int $userid
     *
     * @return array
     */
    public function getTmpAttachByUserid($userid)
    {
        return $this->_getDao()->getTmpAttachByUserid($userid);
    }

    /**
     * 统计帖子中某个类型的附件的个数.
     *
     * @param int    $tid  帖子id
     * @param int    $pid  回复id
     * @param string $type 附件类型
     *
     * @return int
     */
    public function countType($tid, $pid, $type)
    {
        return $this->_getDao()->countType($tid, $pid, $type);
    }

    /**
     * 获取多个tid下的所有附件.
     *
     * @param array tids
     *
     * @return array
     */
    public function fetchAttachByTid($tids)
    {
        if (!$tids || !is_array($tids)) {
            return [];
        }

        return $this->_getDao()->fetchAttachByTid($tids);
    }

    /**
     * 根据指定tid,pid,获取多个附件.
     *
     * @param array tids
     * @param array pids
     *
     * @return array
     */
    public function fetchAttachByTidAndPid($tids, $pids)
    {
        if (!$tids || !$pids || !is_array($tids) || !is_array($pids)) {
            return [];
        }

        return $this->_getDao()->fetchAttachByTidAndPid($tids, $pids);
    }

    /**
     * 获取多个帖子中的指定楼层的附件信息.
     *
     * @param array $tids 帖子序列
     * @param int   $pid  指定楼层
     *
     * @return array
     */
    public function fetchAttachByTidsAndPid($tids, $pid = 0)
    {
        if (empty($tids) || !is_array($tids)) {
            return [];
        }
        $pid = intval($pid);

        return $this->_getDao()->fetchAttachByTidsAndPid($tids, $pid);
    }

    /**
     * 增加一个附件.
     *
     * @param PwThreadAttachDm $dm
     *
     * @return bool|PwError
     */
    public function addAttach(PwThreadAttachDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        if (($result = $this->_getAttach()->addAttach($dm)) instanceof PwError) {
            return $result;
        }
        $dm->setAid($result);
        $this->_getDao()->addAttach($dm->getData());

        return $result;
    }

    /**
     * 更新附件信息.
     *
     * @param PwThreadAttachDm $dm
     *
     * @return bool
     */
    public function updateAttach(PwThreadAttachDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        if (($result = $this->_getAttach()->updateAttach($dm)) instanceof PwError) {
            return $result;
        }

        return $this->_getDao()->updateAttach($dm->aid, $dm->getData(), $dm->getIncreaseData());
    }

    public function updateFid($fid, $tofid)
    {
        return $this->_getDao()->updateFid($fid, $tofid);
    }

    /**
     * 批量更新附件信息.
     *
     * @param array            $aids
     * @param PwThreadAttachDm $dm
     *
     * @return bool
     */
    public function batchUpdateAttach($aids, PwThreadAttachDm $dm)
    {
        if (!$aids || !is_array($aids)) {
            return false;
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        if (($result = $this->_getAttach()->batchUpdateAttach($aids, $dm)) instanceof PwError) {
            return $result;
        }

        return $this->_getDao()->batchUpdateAttach($aids, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 更新多个帖子的所属版块.
     *
     * @param array $tids
     * @param int   $fid
     *
     * @return bool
     */
    public function batchUpdateFidByTid($tids, $fid)
    {
        if (!$tids || !is_array($tids)) {
            return false;
        }

        return $this->_getDao()->batchUpdateFidByTid($tids, $fid);
    }

    /**
     * 删除单个附件.
     *
     * @param int $aid
     *
     * @return bool
     */
    public function deleteAttach($aid)
    {
        $this->_getAttach()->deleteAttach($aid);

        return $this->_getDao()->deleteAttach($aid);
    }

    /**
     * 删除多个附件.
     *
     * @param array $aids
     *
     * @return bool
     */
    public function batchDeleteAttach($aids)
    {
        if (!$aids || !is_array($aids)) {
            return false;
        }
        $this->_getAttach()->batchDeleteAttach($aids);

        return $this->_getDao()->batchDeleteAttach($aids);
    }

    protected function _getAttach()
    {
        return Wekit::load('attach.PwAttach');
    }

    /**
     * @return PwThreadAttachDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('attach.dao.PwThreadAttachDao');
    }
}
