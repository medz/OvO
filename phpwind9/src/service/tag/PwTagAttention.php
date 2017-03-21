<?php
/**
 * 话题关注DS.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwTagAttention
{
    /**
     * 统计我关注的话题.
     *
     * @param int $uid
     *
     * @return array
     */
    public function countAttentionByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return 0;
        }

        return $this->_getTagAttentionDao()->countByUid($uid);
    }

    /**
     * 获取我关注的话题.
     *
     * @param int $uid
     *
     * @return array
     */
    public function getAttentionByUid($uid)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return [];
        }

        return $this->_getTagAttentionDao()->getByUid($uid);
    }

    /**
     * 判断是否关注某话题.
     *
     * @param int   $uid
     * @param array $tagId
     *
     * @return bool
     */
    public function isAttentioned($uid, $tagId)
    {
        $uid = intval($uid);
        $tagId = intval($tagId);
        if ($uid < 1 || $tagId < 1) {
            return false;
        }

        return (bool) $this->_getTagAttentionDao()->get($uid, $tagId);
    }

    /**
     * 根据tagIds获取用户的关注话题.
     *
     * @param int   $uid
     * @param array $tagIds
     */
    public function getAttentionByUidAndTagsIds($uid, $tagIds)
    {
        $uid = intval($uid);
        if ($uid < 1 || ! is_array($tagIds) || ! count($tagIds)) {
            return [];
        }

        return $this->_getTagAttentionDao()->getAttentionByUidAndTagsIds($uid, $tagIds);
    }

    /**
     * 统计关注话题的用户.
     *
     * @param int $tagId
     *
     * @return array
     */
    public function countAttentionByTagId($tagId)
    {
        $tagId = intval($tagId);
        if ($tagId < 1) {
            return 0;
        }

        return $this->_getTagAttentionDao()->countByTagId($tagId);
    }

    /**
     * 获取关注话题的用户.
     *
     * @param int $tagId
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getAttentionUids($tagId, $start, $limit)
    {
        $tagId = intval($tagId);
        if ($tagId < 1) {
            return [];
        }

        return $this->_getTagAttentionDao()->getByTagId($tagId, $start, $limit);
    }

    /**
     * 关注话题.
     *
     * @param int $uid
     * @param int $tagId
     *
     * @return array
     */
    public function addAttention($uid, $tagId)
    {
        $uid = intval($uid);
        $tagId = intval($tagId);
        if ($uid < 1 || $tagId < 1) {
            return false;
        }

        return $this->_getTagAttentionDao()->add(['uid' => $uid, 'tag_id' => $tagId]);
    }

    /**
     * 删除一条关注.
     *
     * @param int $uid
     * @param int $tagId
     *
     * @return array
     */
    public function deleteAttention($uid, $tagId)
    {
        $tagId = intval($tagId);
        $uid = intval($uid);
        if ($tagId < 1 || $uid < 1) {
            return [];
        }

        return $this->_getTagAttentionDao()->delete($uid, $tagId);
    }

    /**
     * 批量删除关注.
     *
     * @param array $tagIds
     */
    public function deleteAttentions($tagIds)
    {
        if (! is_array($tagIds) || ! count($tagIds)) {
            return false;
        }
        $this->_getTagAttentionDao()->deleteByTagIds($tagIds);

        return true;
    }

    /**
     * @return PwTagAttentionDao
     */
    protected function _getTagAttentionDao()
    {
        return Wekit::loadDao('tag.dao.PwTagAttentionDao');
    }
}
