<?php

/**
 * 用户在线服务
 *
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwUserOnline.php 17060 2012-08-31 01:50:31Z gao.wanggao $
 */
class PwUserOnline
{
    /**
     * 判断一个用户是否在线
     *
     * @param int $uid
     *
     * @return bool
     */
    public function isOnline($uid)
    {
        $uid = (int) $uid;
        $data = $this->_getUserOnlineDao()->getInfo($uid);

        return  empty($data) ? false : true;
    }

    /**
     * 判断多个用户是否在线
     *
     * @param array $uids
     *
     * @return array 返回在线用户UID的数组
     */
    public function isOnlines($uids)
    {
        if (! is_array($uids) || ! count($uids)) {
            return [];
        }
        $data = $this->_getUserOnlineDao()->fetchUserOnline($uids);

        return array_keys($data);
    }

    /**
     * 获取一条用户在线信息.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getInfo($uid)
    {
        $uid = (int) $uid;

        return $this->_getUserOnlineDao()->getInfo($uid);
    }

    /**
     * 批量取得用户在线信息.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fetchUserOnline($uids)
    {
        if (! is_array($uids) || ! count($uids)) {
            return false;
        }

        return $this->_getUserOnlineDao()->fetchInfo($uids);
    }

    /**
     * 分页取得在线用户ID.
     *
     * @param int $size
     * @param int $page
     *
     * @return array
     */
    public function getInfoList($fid = 0, $start = 0, $limit = 10)
    {
        $fid = (int) $fid;
        $limit = (int) $limit;
        $start = (int) $start;

        return $this->_getUserOnlineDao()->getInfoList($fid, $start, $limit);
    }

    /**
     * 统计在线用户.
     *
     * @param int $fid
     * @param int $tid
     *
     * @return int
     */
    public function getOnlineCount($fid = 0, $tid = 0)
    {
        $fid = (int) $fid;
        $tid = (int) $tid;

        return $this->_getUserOnlineDao()->getOnlineCount($fid, $tid);
    }

    /**
     * 添加一条在线信息.
     *
     * @param PwOnlineDm $dm
     *
     * @return bool
     */
    public function replaceInfo(PwOnlineDm $dm)
    {
        $resource = $dm->beforeAdd();
        if ($resource instanceof PwError) {
            return $resource;
        }

        return $this->_getUserOnlineDao()->replaceInfo($dm->getData());
    }

    /**
     * 删除一条在线信息.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function deleteInfo($uid)
    {
        $uid = (int) $uid;
        if ($uid < 0) {
            return false;
        }

        return $this->_getUserOnlineDao()->deleteInfo($uid);
    }

    /**
     * 删除多条在线信息.
     *
     * @param array $uids
     *
     * @return int
     */
    public function deleteInfos($uids)
    {
        if (! is_array($uids) || ! count($uids)) {
            return false;
        }

        return $this->_getUserOnlineDao()->deleteInfos($uids);
    }

    /**
     * 删除过期的在线信息.
     *
     * @param int $modify_time
     *
     * @return int
     */
    public function deleteInfoByTime($modify_time)
    {
        $modify_time = (int) $modify_time;
        if ($modify_time < 0) {
            return false;
        }

        return $this->_getUserOnlineDao()->deleteByLastTime($modify_time);
    }

    private function _getUserOnlineDao()
    {
        return Wekit::loadDao('online.dao.PwUserOnlineDao');
    }
}
