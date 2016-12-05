<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForum.php 20973 2012-11-22 10:33:45Z jieyin $
 */
class PwForum
{
    const FETCH_MAIN = 1;        //版块主要信息
    const FETCH_STATISTICS = 2;    //版块统计信息
    const FETCH_EXTRA = 4;        //版块扩展信息
    const FETCH_ALL = 7;

    /**
     * 获取版块信息.
     *
     * @param int $fid 版块id
     * @param int $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                       return array
     */
    public function getForum($fid, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($fid)) {
            return array();
        }

        return $this->_getDao($fetchmode)->getForum($fid);
    }

    /**
     * 批量获取版块信息.
     *
     * @param array $fids 版块id
     * @param int   $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                         return array
     */
    public function fetchForum($fids, $fetchmode = self::FETCH_MAIN)
    {
        if (empty($fids)) {
            return array();
        }

        return $this->_getDao($fetchmode)->fetchForum($fids);
    }

    /**
     * 获取所有版块信息.
     *
     * @param int $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                       return array
     */
    public function getForumList($fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getForumList();
    }

    /**
     * 获取普通版块信息 <不包括子版>.
     *
     * @param int $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                       return array
     */
    public function getCommonForumList($fetchmode = self::FETCH_MAIN)
    {
        return $this->_getDao($fetchmode)->getCommonForumList();
    }

    /**
     * 获取版块排序.
     *
     * @return array
     */
    public function getForumOrderByType($asc = true)
    {
        return $this->_getDao(self::FETCH_MAIN)->getForumOrderByType($asc);
    }

    /**
     * 增加版块.
     *
     * @param object $forumModel 版块数据模型
     *                           return mixed
     */
    public function addForum(PwForumDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao(self::FETCH_ALL)->addForum($dm->getData());
    }

    /**
     * 搜索版块名称.
     *
     * @param string $keyword
     */
    public function searchForum($keyword)
    {
        return $this->_getDao(self::FETCH_MAIN)->searchForum($keyword);
    }

    public function searchDesignForum(PwForumSo $so, $limit = 20, $offset = 0)
    {
        return $this->_getDesignForumDao()->searchForum($so->getData(), $so->getOrderby(), $limit, $offset);
    }

    public function countSearchForum(PwForumSo $so)
    {
        return $this->_getDesignForumDao()->countSearchForum($so->getData());
    }

    /**
     * 更新版块信息.
     *
     * @param object $dm 更新信息
     * @param int    $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                          return bool
     */
    public function updateForum(PwForumDm $dm, $fetchmode = self::FETCH_ALL)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao($fetchmode)->updateForum($dm->fid, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 获取子版块.
     *
     * @param int $fid
     *
     * @return array
     */
    public function getSubForums($fid)
    {
        return $this->_getDao()->getSubForums($fid);
    }

    /**
     * 更新统计版块的帖子统计数.
     *
     * @param int $fid
     */
    public function updateForumStatistics($fid)
    {
        if (empty($fid)) {
            return;
        }
        $this->_getDao(self::FETCH_STATISTICS)->updateForumStatistics($fid, array_keys($this->getSubForums($fid)));
    }

    /**
     * 批量更新版块信息.
     *
     * @param array  $fids 版块id序列
     * @param object $dm   更新信息
     * @param int    $fetchmode 版块资料 <必然为FETCH_*的一种或者组合>
     *                          return bool
     */
    public function batchUpdateForum($fids, PwForumDm $dm, $fetchmode = self::FETCH_ALL)
    {
        if (empty($fids)) {
            return false;
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao($fetchmode)->batchUpdateForum($fids, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 删除一个版块.
     *
     * @param int $fid 版块id
     *
     * @return bool
     */
    public function deleteForum($fid)
    {
        if (empty($fid)) {
            return false;
        }

        return $this->_getDao(self::FETCH_ALL)->deleteForum($fid);
    }

    protected function _getDaoMap()
    {
        return array(
            self::FETCH_MAIN       => 'forum.dao.PwForumDao',
            self::FETCH_STATISTICS => 'forum.dao.PwForumStatisticsDao',
            self::FETCH_EXTRA      => 'forum.dao.PwForumExtraDao',
        );
    }

    protected function _getDao($fetchmode = self::FETCH_MAIN)
    {
        return Wekit::loadDaoFromMap($fetchmode, $this->_getDaoMap(), 'PwForum');
    }

    protected function _getDesignForumDao()
    {
        return Wekit::loadDao('forum.dao.PwDesignForumDao');
    }
}
