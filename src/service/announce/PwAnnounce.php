<?php

defined('WEKIT_VERSION') || exit('Forbidden');
/**
 * 公告管理DS服务
 *
 * @author MingXing Sun <mingxing.sun@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwAnnounce.php 5023 2012-02-28 15:37:02Z mingxing.sun $
 */
class PwAnnounce
{
    /**
     * 添加一条公告信息.
     *
     * @param array $fields
     *
     * @return int
     */
    public function addAnnounce(PwAnnounceDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getPwAnnounceDao()->addAnnounce($dm->getData());
    }

    /**
     * 删除一条公告信息.
     *
     * @param int $aid
     *
     * @return bool
     */
    public function deleteAnnounce($aid)
    {
        $aid = (int) $aid;

        return $this->_getPwAnnounceDao()->deleteAnnounce($aid);
    }

    /**
     * 批量删除公告信息.
     *
     * @param array $aids
     *
     * @return bool
     */
    public function batchDeleteAnnounce($aids)
    {
        if (!$aids || !is_array($aids)) {
            return false;
        }

        return $this->_getPwAnnounceDao()->batchDeleteAnnounce($aids);
    }

    /**
     * 更新一条公告信息.
     *
     * @param object PwAnnounceDm
     *
     * @return bool
     */
    public function updateAnnounce(PwAnnounceDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getPwAnnounceDao()->updateAnnounce($dm->aid, $dm->getData());
    }

    /**
     * 获取公告信息.
     *
     * @param $limit
     * @param $offset
     *
     * @return array
     */
    public function getAnnounceOrderByVieworder($limit, $offset)
    {
        return $this->_getPwAnnounceDao()->getAnnounceOrderByVieworder($limit, $offset);
    }

    /**
     * 通过时间获取公告信息
     * 业务为获取正在发布中的公告信息.
     *
     * @param $time
     * @param $offset
     * @param $limit
     *
     * @return array
     */
    public function getAnnounceByTimeOrderByVieworder($time, $limit, $offset)
    {
        $time = (int) $time;

        return $this->_getPwAnnounceDao()->getAnnounceByTimeOrderByVieworder($time, $limit, $offset);
    }

    /**
     * 获取公告数.
     *
     * @return int
     */
    public function countAnnounce()
    {
        return $this->_getPwAnnounceDao()->countAnnounce();
    }

    /**
     * 获取某一时间内的公告数
     * 业务为获取发布中公告的数量值
     *
     * @param int $time
     *
     * @return int
     */
    public function countAnnounceByTime($time)
    {
        $time = (int) $time;

        return $this->_getPwAnnounceDao()->countAnnounceByTime($time);
    }

    /**
     * 获取一条公告信息.
     *
     * @param int $aid
     *
     * @return array
     */
    public function getAnnounce($aid)
    {
        if ($aid < 1) {
            return array();
        }
        $aid = (int) $aid;

        return $this->_getPwAnnounceDao()->getAnnounce($aid);
    }

    /**
     * 获取公告管理DAO层
     *
     * @return PwAnnounceDao
     */
    protected function _getPwAnnounceDao()
    {
        return Wekit::loadDao('announce.dao.PwAnnounceDao');
    }
}
