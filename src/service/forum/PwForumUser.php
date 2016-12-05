<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 版块会员服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwForumUser.php 16168 2012-08-20 10:34:08Z jinlong.panjl $
 */
class PwForumUser
{
    /**
     * 获取用户在某版块的一条加入信息.
     *
     * @param int $uid
     * @param int $fid
     *
     * @return array
     */
    public function get($uid, $fid)
    {
        if (!$uid || !$fid) {
            return array();
        }

        return $this->_getDao()->get($uid, $fid);
    }

    /**
     * 获取某版块的用户.
     *
     * @param int $fid
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getUserByFid($fid, $limit = 12, $offset = 0)
    {
        if (empty($fid)) {
            return array();
        }

        return $this->_getDao()->getUserByFid($fid, $limit, $offset);
    }

    public function countUserByFid($fid)
    {
        if (empty($fid)) {
            return 0;
        }

        return $this->_getDao()->countUserByFid($fid);
    }

    /**
     * 获取某用户加入的版块.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getFroumByUid($uid)
    {
        if (empty($uid)) {
            return array();
        }

        return $this->_getDao()->getFroumByUid($uid);
    }

    /**
     * 加入版块.
     *
     * @param int $uid
     * @param int $fid
     * @param int $time
     *
     * @return bool
     */
    public function join($uid, $fid, $time = 0)
    {
        if (!$uid || !$fid) {
            return false;
        }

        return $this->_getDao()->add(array('uid' => $uid, 'fid' => $fid, 'join_time' => $time ? $time : Pw::getTime()));
    }

    /**
     * 退出版块.
     *
     * @param int $uid
     * @param int $fid
     *
     * @return bool
     */
    public function quit($uid, $fid)
    {
        if (!$uid || !$fid) {
            return false;
        }

        return $this->_getDao()->delete($uid, $fid);
    }

    /**
     * PwForumUserDao.
     *
     * @return PwForumUserDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('forum.dao.PwForumUserDao');
    }
}
