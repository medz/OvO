<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>.
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwLikeLog.php 8487 2012-04-19 08:09:57Z gao.wanggao $
 */
class PwLikeLog
{
    /**
     * 根据logid获取内容.
     *
     * @param int $logid
     */
    public function getLikeLog($logid)
    {
        $logid = (int) $logid;
        if ($logid < 1) {
            return [];
        }

        return $this->_getLikeLogDao()->getInfo($logid);
    }

    /**
     * 根据用户ID和喜欢ID获取内容.
     *
     * @param unknown_type $uid
     * @param unknown_type $likeid
     */
    public function getInfoByUidLikeid($uid, $likeid)
    {
        $uid = (int) $uid;
        $likeid = (int) $likeid;
        if ($uid < 1 || $likeid < 1) {
            return [];
        }

        return $this->_getLikeLogDao()->getInfoByUidLikeid($uid, $likeid);
    }

    /**
     * 获取多条喜欢内容.
     *
     * @param array $logids
     */
    public function fetchLikeLog($logids)
    {
        if (!is_array($logids) || count($logids) < 1) {
            return [];
        }

        return $this->_getLikeLogDao()->fetchInfo($logids);
    }

    /**
     * 分页获取喜欢列表.
     *
     * @param int|array $uids
     * @param int       $start
     * @param int       $limit
     */
    public function getInfoList($uids, $start = 0, $limit = 10)
    {
        if (!is_array($uids)) {
            $uids = [(int) $uids];
        }
        if (count($uids) < 1) {
            return [];
        }
        $limit = (int) $limit;
        $start = (int) $start;

        return $this->_getLikeLogDao()->getInfoList($uids, $start, $limit);
    }

    /**
     * 统计喜欢数.
     *
     * @param int $uid
     * @param int $tagid
     */
    public function getLikeCount($uid)
    {
        $uid = (int) $uid;

        return $this->_getLikeLogDao()->getLikeCount($uid);
    }

    /**
     * 按时间统计喜欢数.
     *
     * @param int $likeid
     * @param int $time
     */
    public function getLikeidCount($likeid, $time)
    {
        $likeid = (int) $likeid;
        $time = (int) $time;

        return $this->_getLikeLogDao()->getLikeidCount($likeid, $time);
    }

    /**
     * 增加内容.
     *
     * @param PwLikeDm $dm
     */
    public function addInfo(PwLikeLogDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getLikeLogDao()->addInfo($dm->getData());
    }

    /**
     * 更新内容.
     *
     * @param int      $logid
     * @param PwLikeDm $dm
     */
    public function updateInfo(PwLikeLogDm $dm)
    {
        $resource = $dm->beforeUpdate();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getLikeLogDao()->updateInfo($dm->logid, $dm->getData());
    }

    /**
     * 删除信息.
     *
     * @param int $logid
     */
    public function deleteInfo($logid)
    {
        $logid = (int) $logid;
        if ($logid < 1) {
            return false;
        }

        return $this->_getLikeLogDao()->deleteInfo($logid);
    }

    private function _getLikeLogDao()
    {
        return Wekit::loadDao('like.dao.PwLikeLogDao');
    }
}
