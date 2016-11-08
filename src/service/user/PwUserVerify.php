<?php

/**
 * 手机验证
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package wind
 */
class PwUserVerify
{
    /**
     * 取一条
     *
     * @param  int   $uid
     * @return array
     */
    public function getByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return array();
        }

        return $this->_getUserVerify()->get($uid);
    }

    /**
     * 批量取
     *
     * @param  array $uids
     * @return array
     */
    public function fetchByUid($uids)
    {
        if (!is_array($uids) || !$uids) {
            return array();
        }

        return $this->_getUserVerify()->fetch($uids);
    }

    /**
     * 根据手机号码取一条
     *
     * @param  int   $mobile
     * @return array
     */
    public function getByMobile($mobile)
    {
        $mobile = intval($mobile);
        if ($mobile < 1) {
            return array();
        }

        return $this->_getUserVerify()->getByMobile($mobile);
    }

    /**
     * 添加单条
     *
     * @param  int   $uid
     * @param  int   $mobile
     * @return array
     */
    public function addMobile($uid, $mobile)
    {
        $uid = intval($uid);
        $mobile = intval($mobile);
        if ($uid < 1 || $mobile < 1) {
            return false;
        }

        return $this->_getUserVerify()->add(array('uid' => $uid, 'mobile' => $mobile));
    }

    /**
     * 删除单条
     *
     * @param  int  $uid
     * @return bool
     */
    public function deleteByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return false;
        }

        return $this->_getUserVerify()->delete($uid);
    }

    /**
     * 批量删除
     *
     * @param  array $uids
     * @return bool
     */
    public function batchDelete($uids)
    {
        if (!is_array($uids) || !$uids) {
            return false;
        }

        return $this->_getUserVerify()->batchDelete($uids);
    }

    /**
     * 批量删除
     *
     * @param  int  $uid
     * @param  int  $mobile
     * @return bool
     */
    public function updateMobile($uid, $mobile)
    {
        $uid = intval($uid);
        $mobile = intval($mobile);
        if ($uid < 1 || $mobile < 1) {
            return false;
        }

        return $this->_getUserVerify()->update($uid, array('mobile' => intval($mobile)));
    }

    /**
     * @return PwUserVerifyDao
     */
    protected function _getUserVerify()
    {
        return Wekit::loadDao('user.dao.PwUserVerifyDao');
    }
}
