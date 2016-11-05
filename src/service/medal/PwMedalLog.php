<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: liusanbian $>
 * @author $Author: liusanbian $ Foxsee@aliyun.com
 * @copyright  ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwMedalLog.php 12232 2012-06-19 17:37:18Z liusanbian $
 * @package
 */

class PwMedalLog
{
    const STATUS_NO = 0; //无状态
    const STATUS_DOING = 1; //正在进行
    const STATUS_APPLIED = 2; //已申请
    const STATUS_AWARD = 3; //可领取
    const STATUS_AWARDED = 4; //已领取
    /**
     * 获取一条记录
     *
     * @param int $logId
     */
    public function getMedalLog($logId)
    {
        $logId = (int) $logId;
        if ($logId < 1) {
            return array();
        }

        return $this->_getDao()->getInfo($logId);
    }

    /**
     * 根据用户ID和勋章ID获取一条记录
     *
     * @param uid $uid
     * @param uid $medalId
     */
    public function getInfoByUidMedalId($uid, $medalId)
    {
        $uid = (int) $uid;
        $medalId = (int) $medalId;
        if ($uid < 1 || $medalId < 1) {
            return array();
        }

        return $this->_getDao()->getInfoByUidMedalId($uid, $medalId);
    }

    public function fetchMedalLog($logids)
    {
        if (!is_array($logids) && count($logids) < 1) {
            return array();
        }

        return $this->_getDao()->fetchMedalLog($logids);
    }

    public function getInfoListByUid($uid)
    {
        $uid = (int) $uid;
        if ($uid < 1) {
            return array();
        }

        return $this->_getDao()->getInfoListByUid($uid);
    }

    public function getInfoListByUidStatus($uid, $status = self::STATUS_NO)
    {
        $uid = (int) $uid;
        $status = (int) $status;
        if ($uid < 1) {
            return array();
        }

        return $this->_getDao()->getInfoListByUidStatus($uid, $status);
    }
    /**
     * 分页获取内容
     *
     * @param int $uid
     * @param int $process
     * @param int $medalId
     * @param int $start
     * @param int $perpage
     */
    public function getInfoList($uid = 0, $status = self::STATUS_NO, $medalId = 0, $start = 0, $perpage = 10)
    {
        $uid = (int) $uid;
        $status = (int) $status;
        $medalId = (int) $medalId;
        $start = (int) $start;
        $perpage = (int) $perpage;

        return $this->_getDao()->getInfoList($uid, $status, $medalId, $start, $perpage);
    }

    /**
     * 勋章记录搜索，仅供后台调用
     *
     * @param int   $uid
     * @param int   $status
     * @param array $medalIds
     * @param int   $start
     * @param int   $perpage
     */
    public function getMedalLogList($uid = 0, $status = self::STATUS_NO, $medalIds = array(), $start = 0, $perpage = 10)
    {
        $uid = (int) $uid;
        $status = (int) $status;
        !is_array($medalIds) && $medalIds = array();
        $start = (int) $start;
        $perpage = (int) $perpage;

        return $this->_getDao()->getMedalLogList($uid, $status, $medalIds, $start, $perpage);
    }

    /**
     * 内容统计
     *
     * @param int $uid
     * @param int $process
     * @param int $medalId
     */
    public function countMedalLogList($uid = 0, $status = self::STATUS_NO, $medalIds = array())
    {
        $uid = (int) $uid;
        $status = (int) $status;
        !is_array($medalIds) && $medalIds = array();

        return $this->_getDao()->countMedalLogList($uid, $status, $medalIds);
    }

    /**
     * 内容统计
     *
     * @param int $uid
     * @param int $process
     * @param int $medalId
     */
    public function countInfo($uid = 0, $status = self::STATUS_NO, $medalId = 0)
    {
        $uid = (int) $uid;
        $status = (int) $status;
        $medalId = (int) $medalId;

        return $this->_getDao()->countInfo($uid, $status, $medalId);
    }
    /*
    public function addInfo(PwMedalLogDm $dm) {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) return $resource;
        return $this->_getDao()->addInfo($dm->getData());
    }
    */


    public function replaceMedalLog(PwMedalLogDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->replace($dm->getData());
    }

    public function updateInfo(PwMedalLogDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getDao()->updateInfo($dm->logid, $dm->getData());
    }

    /**
     * 根据用户ID和勋章ID更新有效期
     * Enter description here ...
     * @param unknown_type $uid
     * @param unknown_type $medalId
     */
    public function updateExpiredByUidMedalId($uid, $medalId, $time)
    {
        $uid = (int) $uid;
        $medalId = (int) $medalId;
        $time = (int) $time;
        if ($uid < 1 || $medalId < 1) {
            return false;
        }

        return $this->_getDao()->updateExpiredByUidMedalId($uid, $medalId, $time);
    }

    public function deleteInfo($logId)
    {
        $logId = (int) $logId;
        if ($logId < 1) {
            return false;
        }

        return $this->_getDao()->deleteInfo($logId);
    }

    public function deleteInfos($expiredTime = 0, $awardStatus = self::STATUS_AWARDED)
    {
        $expiredTime = (int) $expiredTime;
        if ($expiredTime < 1) {
            return false;
        }

        return $this->_getDao()->deleteInfos($expiredTime, $awardStatus);
    }

    public function deleteInfosByUidMedalIds($uid, $medalIds = array())
    {
        if ($uid < 1 || count($medalIds) < 1) {
            return false;
        }

        return $this->_getDao()->deleteInfosByUidMedalIds($uid, $medalIds);
    }

    public function deleteInfoByMedalId($medalId)
    {
        $medalId = (int) $medalId;
        if ($medalId < 1) {
            return array();
        }

        return $this->_getDao()->deleteInfoByMedalId($medalId);
    }

    private function _getDao()
    {
        return Wekit::loadDao('medal.dao.PwMedalLogDao');
    }
}
