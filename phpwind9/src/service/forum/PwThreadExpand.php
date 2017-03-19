<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子扩展服务,不经常用的接口.
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwThreadExpand.php 22254 2012-12-20 08:03:33Z jinlong.panjl $
 */
class PwThreadExpand
{
    /**
     * 获取版块(A)中搜索帖子最后回复时间大于(B)的帖子.
     *
     * @param int $fid
     * @param int $lastpostTime
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getThreadByFidOverTime($fid, $lastpostTime, $limit = 10, $offset = 0)
    {
        $fid = intval($fid);
        if (empty($fid)) {
            return [];
        }

        return $this->_getThreadDao()->getThreadByFidOverTime($fid, $lastpostTime, $limit, $offset);
    }

    /**
     * 获取版块(A)中搜索帖子最后回复时间小于(B)的帖子.
     *
     * @param int $fid
     * @param int $lastpostTime
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getThreadByFidUnderTime($fid, $lastpostTime, $limit = 10, $offset = 0)
    {
        $fid = intval($fid);
        if (empty($fid)) {
            return [];
        }

        return $this->_getThreadDao()->getThreadByFidUnderTime($fid, $lastpostTime, $limit, $offset);
    }

    /**
     * 获取多个用户的帖子.
     *
     * @param array $uids uid序列
     *
     * @return array
     */
    public function fetchThreadByUid($uids)
    {
        if (empty($uids) || !is_array($uids)) {
            return [];
        }

        return $this->_getThreadDao()->fetchThreadByUid($uids);
    }

    /**
     * 统计用户某段时间在版块(A)发帖数排行.
     *
     * @param int $fid
     * @param int $time
     * @param int $num
     *
     * @return array
     */
    public function countUserThreadByFidAndTime($fid, $time, $num)
    {
        $fid = intval($fid);
        if (empty($fid)) {
            return [];
        }

        return $this->_getThreadDao()->countUserThreadByFidAndTime($fid, $time, $num);
    }

    /**
     * 统计用户某段时间在版块(A)回复数排行.
     *
     * @param int $fid
     * @param int $time
     * @param int $num
     *
     * @return array
     */
    public function countUserPostByFidAndTime($fid, $time, $num)
    {
        $fid = intval($fid);
        if (empty($fid)) {
            return [];
        }

        return $this->_getPostDao()->countUserPostByFidAndTime($fid, $time, $num);
    }

    public function countThreadsByFid()
    {
        return $this->_getThreadDao()->countThreadsByFid();
    }

    public function countPostsByFid()
    {
        return $this->_getPostDao()->countPostsByFid();
    }

    /**
     * 根据uid统计审核和未审核的帖子.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countDisabledThreadByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return 0;
        }

        return $this->_getThreadDao()->countDisabledThreadByUid($uid);
    }

    /**
     * 根据uid获取审核和未审核的帖子.
     *
     * @param int $uid       用户id
     * @param int $limit     个数
     * @param int $offset    起始偏移量
     * @param int $fetchmode 帖子资料 <必然为FETCH_*的一种或者组合>
     *                       return array
     */
    public function getDisabledThreadByUid($uid, $limit = 0, $offset = 0)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return [];
        }

        return $this->_getThreadDao()->getDisabledThreadByUid($uid, $limit, $offset);
    }

    /**
     * 统计用户的审核和未审核回复数.
     *
     * @param int $uid
     *
     * @return int
     */
    public function countDisabledPostByUid($uid)
    {
        if (empty($uid)) {
            return 0;
        }

        return $this->_getPostDao()->countDisabledPostByUid($uid);
    }

    /**
     * 获取用户的审核和未审核回复.
     *
     * @param int $uid    用户id
     * @param int $limit  个数
     * @param int $offset 起始偏移量
     *                    return array
     */
    public function getDisabledPostByUid($uid, $limit = 20, $offset = 0)
    {
        if (empty($uid)) {
            return [];
        }

        return $this->_getPostDao()->getDisabledPostByUid($uid, $limit, $offset);
    }

    protected function _getThreadDao()
    {
        return Wekit::loadDao('forum.dao.PwThreadExpandDao');
    }

    protected function _getPostDao()
    {
        return Wekit::loadDao('forum.dao.PwPostExpandDao');
    }
}
