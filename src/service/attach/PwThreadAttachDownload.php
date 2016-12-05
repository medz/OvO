<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 帖子附件购买记录 / ds服务
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwThreadAttachDownload
{
    /**
     * 统计附件的出售额.
     *
     * @param int $aid 附件id
     *
     * @return int
     */
    public function sumCost($aid)
    {
        if (empty($aid)) {
            return 0;
        }

        return $this->_getDao()->sumCost($aid);
    }

    /**
     * 获取一条记录.
     *
     * @param int $id 记录id
     *
     * @return array
     */
    public function get($id)
    {
        if (empty($id)) {
            return array();
        }

        return $this->_getDao()->get($id);
    }

    /**
     * 获取某个附件的所有购买记录.
     *
     * @param int $aid 附件id
     *
     * @return bool
     */
    public function countByAid($aid)
    {
        if (empty($aid)) {
            return array();
        }

        return $this->_getDao()->countByAid($aid);
    }

    /**
     * 获取某个附件的所有购买记录.
     *
     * @param int $aid    附件id
     * @param int $limit  获取列表行数
     * @param int $offset 获取列表开始偏移量
     *
     * @return bool
     */
    public function getByAid($aid, $limit = 20, $offset = 0)
    {
        if (empty($aid)) {
            return array();
        }

        return $this->_getDao()->getByAid($aid, $limit, $offset);
    }

    /**
     * 获取附件(A)中用户(B)的购买记录.
     *
     * @param int $aid 帖子(A)
     * @param int $uid 用户(B)
     *
     * @return bool
     */
    public function getByAidAndUid($aid, $uid)
    {
        if (empty($aid) || empty($uid)) {
            return array();
        }

        return $this->_getDao()->getByAidAndUid($aid, $uid);
    }

    /**
     * 添加一条记录.
     *
     * @param PwThreadBuyDm $dm 帖子购买记录数据模型
     *                          return mixed
     */
    public function add(PwThreadAttachBuyDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao()->add($dm->getData());
    }

    /**
     * Enter description here ...
     *
     * @return PwThreadAttachDownloadDao
     */
    protected function _getDao()
    {
        return Wekit::loadDao('attach.dao.PwThreadAttachDownloadDao');
    }
}
