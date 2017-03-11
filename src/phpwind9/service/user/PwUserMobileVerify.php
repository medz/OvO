<?php

/**
 * 手机验证码
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwUserMobileVerify
{
    /**
     * 取一条
     *
     * @param int $mobile
     *
     * @return array
     */
    public function getMobileVerify($mobile)
    {
        if ($mobile < 1) {
            return array();
        }

        return $this->_getDao()->get($mobile);
    }

    /**
     * 批量取.
     *
     * @param array $mobiles
     *
     * @return array
     */
    public function fetchMobileVerify($mobiles)
    {
        if (!is_array($mobiles) || !$mobiles) {
            return array();
        }

        return $this->_getDao()->fetch($mobiles);
    }

    /**
     * 添加单条
     *
     * @param int $uid
     * @param int $mobile
     *
     * @return array
     */
    public function addMobileVerify(PwUserMobileDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao()->replace($dm->getData());
    }

    /**
     * 删除单条
     *
     * @param int $uid
     *
     * @return bool
     */
    public function delete($mobile)
    {
        if ($mobile < 1) {
            return false;
        }

        return $this->_getDao()->delete($mobile);
    }

    /**
     * 删除过期数据.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function deleteByExpiredTime($expired_time)
    {
        $expired_time = intval($expired_time);

        return $this->_getDao()->deleteByExpiredTime($expired_time);
    }

    /**
     * 批量删除.
     *
     * @param array $mobiles
     *
     * @return bool
     */
    public function batchDelete($mobiles)
    {
        if (!is_array($mobiles) || !$mobiles) {
            return false;
        }

        return $this->_getDao()->batchDelete($mobiles);
    }

    /**
     * 更新.
     *
     * @param int $mobile
     * @param int $mobile
     *
     * @return bool
     */
    public function updateMobile($mobile, PwUserMobileDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->update($mobile, $dm->getData());
    }

    /**
     * 更新.
     *
     * @param int $expiredTime
     * @param int $mobile
     *
     * @return bool
     */
    public function updateByExpiredTime($expiredTime, PwUserMobileDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->updateByExpiredTime($expiredTime, $dm->getData());
    }

    /**
     * @return PwUserMobileVerifyDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('user.dao.PwUserMobileVerifyDao');
    }
}
