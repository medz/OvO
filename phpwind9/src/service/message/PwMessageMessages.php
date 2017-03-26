<?php
/**
 * Enter description here ...
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Jan 9, 2012
 *
 * @link http://www.phpwind.com
 *
 * @copyright 2011 phpwind.com
 * @license
 *
 * @version $Id: PwMessageMessages.php 3407 2012-01-11 09:00:39Z peihong.zhangph $
 */
class PwMessageMessages
{
    /**
     * 获取用户消息配置.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getMessageConfig($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return [];
        }

        return $this->_getMessageConfigDao()->getMessageConfig($uid);
    }

    /**
     * 批量获取用户消息配置.
     *
     * @param array $uids
     *
     * @return array
     */
    public function fetchMessageConfig($uids)
    {
        if (! is_array($uids) || ! count($uids)) {
            return [];
        }

        return $this->_getMessageConfigDao()->fetchMessageConfig($uids);
    }

    /**
     * 用户配置.
     *
     * @param array $data
     *
     * @return int
     */
    public function setMessageConfig($uid, $privacy, $notice_types)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return [];
        }
        $data = [
            'uid'          => $uid,
            'privacy'      => $privacy,
            'notice_types' => $notice_types,
        ];

        return $this->_getMessageConfigDao()->setMessageConfig($data);
    }

    /**
     * Enter description here ...
     *
     * @return PwMessageConfigDao
     */
    private function _getMessageConfigDao()
    {
        return Wekit::loadDao('message.dao.PwMessageConfigDao');
    }
}
