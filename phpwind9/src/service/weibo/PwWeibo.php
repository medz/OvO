<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 微博基础服务
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id: PwWeibo.php 8959 2012-04-28 09:06:05Z jieyin $
 */
class PwWeibo
{
    const TYPE_MEDAL = 1;
    const TYPE_LIKE = 2;

    /**
     * 获取一条微博.
     *
     * @param int $weiboId
     *
     * @return array
     */
    public function getWeibo($weiboId)
    {
        if (empty($weiboId)) {
            return [];
        }

        return $this->_getDao()->getWeibo($weiboId);
    }

    /**
     * 获取微博列表.
     *
     * @param array $weiboIds
     *
     * @return array
     */
    public function getWeibos($weiboIds)
    {
        if (empty($weiboIds) || ! is_array($weiboIds)) {
            return [];
        }

        return $this->_getDao()->fetchWeibo($weiboIds);
    }

    /**
     * 发布一条微博
     * 注：本接口只提供数据层的相关操作，完整的发布微博接口请参照 PwSendWeibo::send().
     *
     * @param object $dm PwWeiboDm
     *
     * @return bool|PwError
     */
    public function addWeibo(PwWeiboDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getDao()->addWeibo($dm->getData());
    }

    /**
     * 更新一条微博.
     *
     * @param object $dm PwWeiboDm
     *
     * @return bool|PwError
     */
    public function updateWeibo(PwWeiboDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getDao()->updateWeibo($dm->weibo_id, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 删除一条微博.
     *
     * @param int $weiboId
     *
     * @return bool
     */
    public function deleteWeibo($weiboId)
    {
        if (! $weiboId) {
            return false;
        }

        return $this->_getDao()->deleteWeibo($weiboId);
    }

    /**
     * 删除多条微博
     * 注：本接口只提供数据层的相关操作，完整的微博删除接口请参照 PwWeiboService::batchDeleteWeibo().
     *
     * @param array $weiboIds
     *
     * @return bool
     */
    public function batchDeleteWeibo($weiboIds)
    {
        if (empty($weiboIds) || ! is_array($weiboIds)) {
            return false;
        }

        return $this->_getDao()->batchDeleteWeibo($weiboIds);
    }

    /**
     * 获取评论列表.
     *
     * @param int  $weiboId 微博id
     * @param int  $limit   获取条数
     * @param int  $offset  数据条目偏移量
     * @param bool $asc     排序
     *
     * @return array
     */
    public function getComment($weiboId, $limit, $offset = 0, $asc = true)
    {
        return $this->_getCommentDao()->getComment($weiboId, $limit, $offset, $asc);
    }

    /**
     * 添加一条微博评论
     * 注：本接口只提供数据层的相关操作，完整的发布微博评论接口请参照 PwWeiboService::addComment().
     *
     * @param object $dm PwWeiboCommnetDm
     *
     * @return bool|PwError
     */
    public function addComment(PwWeiboCommnetDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getCommentDao()->addComment($dm->getData());
    }

    /**
     * 删除多条微博的评论.
     *
     * @param array $weiboIds
     *
     * @return bool
     */
    public function batchDeleteCommentByWeiboId($weiboIds)
    {
        if (empty($weiboIds) || ! is_array($weiboIds)) {
            return false;
        }

        return $this->_getCommentDao()->batchDeleteCommentByWeiboId($weiboIds);
    }

    protected function _getDao()
    {
        return Wekit::loadDao('weibo.dao.PwWeiboDao');
    }

    protected function _getCommentDao()
    {
        return Wekit::loadDao('weibo.dao.PwWeiboCommentDao');
    }
}
