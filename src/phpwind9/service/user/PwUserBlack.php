<?php

/**
 * 用户黑名单.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwUserBlack
{
    /**
     * 获取用户黑名单.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getBlacklist($uid)
    {
        return $this->_getWindidUserBlack()->getBlack($uid);
    }

    /**
     * 批量获取用户黑名单.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fetchBlacklist($uids)
    {
        return $this->_getWindidUserBlack()->fetchBlack($uids);
    }

    /**
     * 设置用户黑名单.
     *
     * @param int $uid
     * @param int $blackUid
     */
    public function setBlacklist($uid, $blackUid)
    {
        return $this->_getWindidUserBlack()->addBlack($uid, $blackUid);
    }

    /**
     * 替换黑名单.
     *
     * @param int   $uid
     * @param array $blackList
     */
    public function replaceBlack($uid, $blackList)
    {
        return $this->_getWindidUserBlack()->replaceBlack($uid, $blackList);
    }

    /**
     * 删除.
     *
     * @param int $uid
     *
     * @return bool
     */
    public function deleteBlacklist($uid)
    {
        return $this->_getWindidUserBlack()->delBlack($uid);
    }

    /**
     * 检测是否黑名单.
     *
     * @param int   $uid
     * @param array $uids
     *
     * @return array | bool
     */
    public function checkUserBlack($uid, $uids)
    {
        !is_array($uids) && $uids = [$uids];
        $blacks = $this->fetchBlacklist($uids);
        $privateBlacks = [];
        foreach ($blacks as $v) {
            if ($v['blacklist']) {
                $blacklist = @unserialize($v['blacklist']);
                in_array($uid, $blacklist) && $privateBlacks[] = $v['uid'];
            }
        }

        return $privateBlacks ? $privateBlacks : false;
    }

    /**
     * @return WindidUserBlack
     */
    protected function _getWindidUserBlack()
    {
        return WindidApi::api('user');
    }
}
