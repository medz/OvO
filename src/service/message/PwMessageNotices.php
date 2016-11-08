<?php
/**
 * Enter description here ...
 *
 * @author peihong.zhangph <peihong.zhangph@aliyun-inc.com> Jan 9, 2012
 * @link http://www.phpwind.com
 * @copyright 2011 phpwind.com
 * @license
 * @version $Id: PwMessageNotices.php 3407 2012-01-11 09:00:39Z peihong.zhangph $
 */

class PwMessageNotices
{
    /**
     *
     * 获取一条通知内容
     * @param int $id
     */
    public function getNotice($id)
    {
        $notice = $this->_getDao()->getNotice($id);
        $notice['extend_params'] && $notice['extend_params'] = unserialize($notice['extend_params']);

        return $notice;
    }

    /**
     *
     * 获取上一条通知内容
     * @param int $id
     */
    public function getPrevNotice($uid, $id)
    {
        $uid = intval($uid);
        $id = intval($id);

        return $this->_getDao()->getPrevNotice($uid, $id);
    }

    /**
     *
     * 获取上一条通知内容
     * @param int $id
     */
    public function getNextNotice($uid, $id)
    {
        $uid = intval($uid);
        $id = intval($id);

        return $this->_getDao()->getNextNotice($uid, $id);
    }

    /**
     * 根据用户UID获取通知列表 按更新时间倒序
     *
     * @param int $uid
     * @param int $type  类型ID
     * @param int $start 偏移量
     * @param int $num
     */
    public function getNotices($uid, $type = 0, $start = 0, $num = 20)
    {
        return $this->_getDao()->getNotices($uid, $type, $start, $num);
    }

    /**
     *
     * 根据用户UID获取通知列表 按未读升序、更新时间倒序
     * @param int $uid
     * @param int $num
     */
    public function getNoticesOrderByRead($uid, $num)
    {
        return $this->_getDao()->getNoticesOrderByRead($uid, $num);
    }

    /**
     * 获取未读通知数
     * @param int $uid
     */
    public function getUnreadNoticeCount($uid)
    {
        return $this->_getDao()->getUnreadNoticeCount($uid);
    }

    /**
     *
     * 添加通知
     *
     * @param PwMessageNoticesDm $dm
     */
    public function addNotice($dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }
        $fields = $dm->getData();

        return $this->_getDao()->addNotice($fields);
    }

    /**
     * 更新通知信息
     *
     * @param PwMessageNoticesDm $dm
     *                               return bool
     */
    public function updateNotice($dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }
        $fields = $dm->getData();
        if (!$fields) {
            return false;
        }

        return $this->_getDao()->updateNotice($dm->id, $fields);
    }

    /**
     * 更新通知信息
     *
     * @param array              $ids
     * @param PwMessageNoticesDm $dm
     *                                return bool
     */
    public function batchUpdateNotice($ids, $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->batchUpdateNotice($ids, $dm->getData());
    }

    /**
     * 根据uid和type更新通知
     *
     * @param array              $ids
     * @param PwMessageNoticesDm $dm
     *                                return bool
     */
    public function batchUpdateNoticeByUidAndType($uid, $type, $dm)
    {
        $uid = intval($uid);
        $type = intval($type);
        if ($uid < 1 || $type < 1 || ($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->batchUpdateNoticeByUidAndType($uid, $type, $dm->getData());
    }

    /**
     *
     * 删除通知
     *
     * @param int $id
     */
    public function deleteNotice($id)
    {
        return $this->_getDao()->deleteNotice($id);
    }

    /**
     *
     * 批量删除通知
     *
     * @param array $ids
     */
    public function deleteNoticeByIds($ids)
    {
        return $this->_getDao()->deleteNoticeByIds($ids);
    }
    public function deleteNoticeByIdsAndUid($uid, $ids)
    {
        return $this->_getDao()->deleteNoticeByIdsAndUid($uid, $ids);
    }

    /**
     * 根据类型删除通知
     *
     * @param int $uid
     * @param int $type
     * @param int $param
     * @param bool
     */
    public function deleteNoticeByType($uid, $type, $param)
    {
        return $this->_getDao()->deleteNoticeByType($uid, $type, $param);
    }

    /**
     * 根据uid删除通知
     *
     * @param int $uid
     * @param bool
     */
    public function deleteNoticeByUid($uid)
    {
        return $this->_getDao()->deleteNoticeByUid($uid);
    }

    /**
     * 根据类型批量删除通知
     *
     * @param int   $uid
     * @param int   $type
     * @param array $params
     * @param bool
     */
    public function betchDeleteNoticeByType($uid, $type, $params)
    {
        return $this->_getDao()->betchDeleteNoticeByType($uid, $type, $params);
    }

    public function getNoticeByUid($uid, $type, $param)
    {
        return $this->_getDao()->getNoticeByUid($uid, $type, $param);
    }

    public function countNoticesByType($uid)
    {
        return $this->_getDao()->countNoticesByType($uid);
    }

    /**
     * Enter description here ...
     * @return PwMessageNoticesDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('message.dao.PwMessageNoticesDao');
    }
}
