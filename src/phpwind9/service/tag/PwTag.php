<?php
/**
 * 话题DS.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 *
 * @version $Id$
 */
class PwTag
{
    const TYPE_THREAD_TOPIC = 1; //话题类型-帖子
    const TYPE_THREAD_REPLY = 2; //话题类型-回复
    const TYPE_WEIBO = 3; //话题类型-微薄

    public $typeMap = array(
        self::TYPE_THREAD_TOPIC => 'threads',
        self::TYPE_THREAD_REPLY => 'posts',
        self::TYPE_WEIBO        => 'weibo',
    );

    /**
     * 添加一条话题.
     *
     * @param PwTagDao $dm
     */
    public function addTag(PwTagDm $dm)
    {
        if (!$dm instanceof PwTagDm) {
            return new PwError('TAG:data_error');
        }
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getTagDao()->addTag($dm->getData());
    }

    /**
     * 更新一条话题.
     *
     * @param PwTagDao $dm
     *                     return bool
     */
    public function updateTag(PwTagDm $dm)
    {
        if (!$dm instanceof PwTagDm) {
            return new PwError('TAG:data_error');
        }
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getTagDao()->update($dm->tag_id, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 批量更新话题.
     *
     * @param array $tagDms
     *                      return bool
     */
    public function batchUpdate($tagDms)
    {
        $data = array();
        foreach ($tagDms as $dm) {
            if (!$dm instanceof PwTagDm) {
                return new PwError('TAG:data_error');
            }
            $tagIds[] = $dm->tag_id;
        }

        return $this->_getTagDao()->batchUpdate($tagIds, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 批量删除话题.
     *
     * @param array $tagIds
     */
    public function batchDelete($tagIds)
    {
        if (!is_array($tagIds) || !count($tagIds)) {
            return false;
        }

        return $this->_getTagDao()->batchDelete($tagIds);
    }

    /**
     * 添加更新统计
     *
     * @param int $tagId
     * @param int $updateTime
     *                        return bool
     */
    public function addTagRecord($tagId, $updateTime)
    {
        $tagId = intval($tagId);
        if ($tagId < 1) {
            return false;
        }
        $data = array(
            'tag_id'      => $tagId,
            'update_time' => $updateTime,
        );

        return $this->_getTagRecordDao()->addTagRecord($data);
    }

    /**
     * 批量添加更新统计
     *
     * @param array $fields
     *                      return bool
     */
    public function batchAddTagRecord($fields)
    {
        if (!is_array($fields) || !$fields) {
            return false;
        }
        $data = array();
        foreach ($fields as $v) {
            $data[] = array(
                'tag_id'      => $v['tag_id'],
                'is_reply'    => $v['is_reply'],
                'update_time' => $v['update_time'],
            );
        }

        return $this->_getTagRecordDao()->batchAddTagRecord($data);
    }

    /**
     * 更新tag update表的tagid.
     *
     * @param int $fromTagId
     * @param int $toTagId
     *
     * @return array
     */
    public function updateTagRecordByTagId($fromTagId, $toTagId)
    {
        $fromTagId = intval($fromTagId);
        $toTagId = intval($toTagId);
        if ($fromTagId < 1 || $toTagId < 1) {
            return false;
        }

        return $this->_getTagRecordDao()->updateTagRecordByTagId($fromTagId, $toTagId);
    }

    /**
     * 更新tag relation表的tagid,content id 合并业务需要
     *
     * @param int $fromTagId
     * @param int $toTagId
     *
     * @return array
     */
    public function updateTagRelationByTagId($fromTagId, $toTagId)
    {
        $fromTagId = intval($fromTagId);
        $toTagId = intval($toTagId);
        if ($fromTagId < 1 || $toTagId < 1) {
            return false;
        }

        return $this->_getTagRelationDao()->updateTagRelationByTagId($fromTagId, $toTagId);
    }

    /**
     * 添加内容关系.
     *
     * @param PwTagDm
     * return bool
     */
    public function addRelation(PwTagDm $dm)
    {
        if (!$dm instanceof PwTagDm) {
            return new PwError('TAG:data_error');
        }
        if ($dm->tag_id < 1) {
            return false;
        }
        $data = $dm->getData();
        if (!$data['param_id']) {
            return false;
        }
        $result = (int) $this->_getTagRelationDao()->addRelation(array_merge(array('tag_id' => $dm->tag_id), $data));
        $this->_getTagDao()->update($dm->tag_id, '', array('content_count' => $result));
    }

    /**
     * 批量添加内容关系.
     *
     * @param array $dms
     *                   return bool
     */
    public function batchAddRelation($dms)
    {
        if (!is_array($dms) || !$dms) {
            return false;
        }
        $data = array();
        foreach ($dms as $dm) {
            if (!$dm instanceof PwTagDm) {
                return new PwError('TAG:data_error');
            }
            $data[] = array_merge(array('tag_id' => $dm->tag_id), $dm->getData());
        }
        if (!$data) {
            return false;
        }

        return $this->_getTagRelationDao()->batchAddRelation($data);
    }

    /**
     * 更新内容关系.
     *
     * @param PwTagDm
     * return bool
     */
    public function updateRelation($typeId, $paramId, $id, $dm)
    {
        if (!$dm instanceof PwTagDm) {
            return new PwError('TAG:data_error');
        }
        if ($id < 1 || $paramId < 1) {
            return false;
        }

        return $this->_getTagRelationDao()->updateRelation($typeId, $paramId, $id, $dm->getData());
    }

    /**
     * 批量删除内容关系.
     *
     * @param int   $typeId
     * @param int   $paramId
     * @param array $tagIds
     *                       return bool
     */
    public function batchDeleteRelationsByType($typeId, $paramId, $tagIds)
    {
        $typeId = intval($typeId);
        $paramId = intval($paramId);
        if ($typeId < 1 || $paramId < 1 || !is_array($tagIds) || !$tagIds) {
            return false;
        }
        $result = $this->_getTagRelationDao()->batchDeleteRelationsByType($typeId, $paramId, $tagIds);
        $this->_getTagDao()->batchUpdate($tagIds, array(), array('content_count' => -1));

        return true;
    }

    /**
     * 删除内容关系.
     *
     * @param int $typeId
     * @param int $paramId
     * @param int $tagId
     *                     return bool
     */
    public function deleteRelation($typeId, $paramId, $tagId)
    {
        $typeId = intval($typeId);
        $paramId = intval($paramId);
        $tagId = intval($tagId);
        if ($typeId < 1 || $tagId < 1) {
            return false;
        }
        $result = $this->_getTagRelationDao()->delete($typeId, $paramId, $tagId);
        $result && $this->_getTagDao()->update($tagId, array(), array('content_count' => -$result));

        return true;
    }

    /**
     * 批量删除内容关系.
     *
     * @param int   $typeId
     * @param array $paramIds
     *
     * @return bool
     */
    public function batchDeleteRelation($typeId, $paramIds)
    {
        $typeId = intval($typeId);
        if ($typeId < 1 || !is_array($paramIds) || !count($paramIds)) {
            return false;
        }

        return $this->_getTagRelationDao()->batchDelete($typeId, $paramIds);
    }

    /**
     * 根据tagId统计内容关系数.
     *
     * @param int $tagId
     * @param int $typeId
     *
     * @return array
     */
    public function countRelationsByTagId($tagId, $typeId, $ifcheck = 1)
    {
        $tagId = intval($tagId);
        $typeId = intval($typeId);
        $ifcheck = intval($ifcheck);
        if ($tagId < 1 || $typeId < 1) {
            return 0;
        }

        return $this->_getTagRelationDao()->countByTagId($tagId, $typeId, $ifcheck);
    }

    /**
     * 清空热门话题数据表.
     *
     * @param array $tagIds
     */
    public function deleteTagRecords($tagIds)
    {
        if (!is_array($tagIds) || !$tagIds) {
            return false;
        }
        $this->_getTagRecordDao()->deleteByTagIds($tagIds);
    }

    /**
     * 删除内容关系数据表.
     *
     * @param array $tagIds
     */
    public function deleteRelations($tagIds)
    {
        if (!is_array($tagIds) || !$tagIds) {
            return false;
        }
        $this->_getTagRelationDao()->deleteByTagIds($tagIds);
    }

    /**
     * 批量修改话题.
     *
     * @param array   $tagIds
     * @param PwTagDm $dm
     */
    public function updateTags($tagIds, PwTagDm $dm)
    {
        if (!is_array($tagIds) || !count($tagIds)) {
            return new PwError('TAG:data_error');
        }

        return $this->_getTagDao()->batchUpdate($tagIds, $dm->getData(), $dm->getIncreaseData());
    }

    /**
     * 获取一条话题.
     *
     * @param int $tagId
     *
     * @return array
     */
    public function getTag($tagId)
    {
        $tagId = intval($tagId);
        if ($tagId < 1) {
            return false;
        }

        return $this->_getTagDao()->getTag($tagId);
    }

    /**
     * 根据归属话题获取话题.
     *
     * @param int $parentTagId
     *
     * @return array
     */
    public function getTagByParent($parentTagId)
    {
        $parentTagId = intval($parentTagId);
        if ($parentTagId < 1) {
            return false;
        }

        return $this->_getTagDao()->getTagByParent($parentTagId);
    }

    /**
     * 根据话题名称获取一条话题.
     *
     * @param string $tagName
     *
     * @return array
     */
    public function getTagByName($tagName)
    {
        return $this->_getTagDao()->getTagByName($tagName);
    }

    /**
     * 根据话题名称批量获取话题.
     *
     * @param array $tagNames
     *
     * @return array
     */
    public function getTagsByNames($tagNames)
    {
        if (!is_array($tagNames) || !count($tagNames)) {
            return array();
        }

        return $this->_getTagDao()->getTagsByNames($tagNames);
    }

    /**
     * 搜索话题count -- 只供后台搜索使用.
     *
     * @param string $name
     * @param int    $ifHot
     * @param int    $categoryId
     * @param int    $attentionCountStart
     * @param int    $attentionCountEnd
     * @param int    $contentCountStart
     * @param int    $contentCountEnd
     *
     * @return int
     */
    public function countTagByCondition($name, $ifHot, $categoryId, $attentionCountStart, $attentionCountEnd, $contentCountStart, $contentCountEnd)
    {
        return $this->_getTagDao()->countTagByCondition($name, $ifHot, $categoryId, $attentionCountStart, $attentionCountEnd, $contentCountStart, $contentCountEnd);
    }

    /**
     * 搜索话题列表 -- 只供后台搜索使用.
     *
     * @param int    $start
     * @param int    $limit
     * @param string $name
     * @param int    $ifHot
     * @param int    $categoryId
     * @param int    $attentionCountStart
     * @param int    $attentionCountEnd
     * @param int    $contentCountStart
     * @param int    $contentCountEnd
     *
     * @return array
     */
    public function getTagByCondition($start, $limit, $name, $ifHot, $categoryId, $attentionCountStart, $attentionCountEnd, $contentCountStart, $contentCountEnd)
    {
        return $this->_getTagDao()->getTagByCondition($start, $limit, $name, $ifHot, $categoryId, $attentionCountStart, $attentionCountEnd, $contentCountStart, $contentCountEnd);
    }

    /**
     * 根据参数获取相关话题.
     */
    public function getTagsByParamIds($typeId, $paramIds)
    {
        $typeId = intval($typeId);
        if ($typeId < 1 || !is_array($paramIds) || !count($paramIds)) {
            return array();
        }

        return $this->_getTagDao()->getTagsByParamIds($typeId, $paramIds);
    }

    /**
     * 删除过期数据.
     *
     * @param int $updateTime
     *
     * @return bool
     */
    public function deleteExpireHotTag($updateTime)
    {
        $updateTime = intval($updateTime);
        if ($updateTime < 1) {
            return false;
        }

        return $this->_getTagRecordDao()->deleteByTime($updateTime);
    }

    /**
     * 统计热门话题榜.
     *
     * @param int $num
     *
     * @return array
     */
    public function getCountHotTag($categoryId, $num)
    {
        $categoryId = intval($categoryId);
        $num = intval($num);
        if ($num < 1) {
            return array();
        }
        if (!$categoryId) {
            return $this->_getTagRecordDao()->getHotTags($num);
        } else {
            return $this->_getTagRecordDao()->getHotTagsByCategory($categoryId, $num);
        }
    }

    /**
     * 根据tagId取话题内容关系.
     *
     * @param int $tagId
     * @param int $typeId
     *
     * @return array
     */
    public function getTagRelation($tagId, $typeId, $ifcheck, $offset, $num = 4)
    {
        $tagId = intval($tagId);
        $typeId = intval($typeId);
        $ifcheck = intval($ifcheck);
        if ($tagId < 1 || $typeId < 1) {
            return array();
        }

        return $this->_getTagRelationDao()->getByTagId($tagId, $typeId, $ifcheck, $offset, $num);
    }

    /**
     * 获取我关注的话题榜.
     *
     * @param int $uid
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getAttentionByUid($uid, $start, $limit)
    {
        $uid = intval($uid);
        if ($uid < 1) {
            return array();
        }

        return $this->_getTagDao()->getAttentionTag($uid, $start, $limit);
    }

    /**
     * 批量获取话题.
     *
     * @param array $tagIds
     *
     * @return array
     */
    public function fetchTag($tagIds)
    {
        if (!is_array($tagIds) || !count($tagIds)) {
            return array();
        }

        return $this->_getTagDao()->fetchTag($tagIds);
    }

    /**
     * 根据应用类型和id获取话题关系.
     *
     * @param int $typeId
     * @param int $paramId
     *
     * @return array
     */
    public function getTagRelationByType($typeId, $paramId)
    {
        $typeId = intval($typeId);
        $paramId = intval($paramId);
        if ($typeId < 1 || $paramId < 1) {
            return array();
        }

        return $this->_getTagRelationDao()->getByTypeId($typeId, $paramId);
    }

    /**
     * 根据应用类型和id获取话题.
     *
     * @param int $typeId
     * @param int $paramId
     *
     * @return array
     */
    public function getTagByType($typeId, $paramId)
    {
        $tagRelations = $this->getTagRelationByType($typeId, $paramId);

        return $this->fetchTag(array_keys($tagRelations));
    }

    /**
     * 根据类型和IDs批量获取数据.
     *
     * @param int   $typeId
     * @param array $paramIds
     *
     * @return array
     */
    public function fetchByTypeIdAndParamIds($typeId, $paramIds)
    {
        if ($typeId < 1 || !is_array($paramIds) || !$paramIds) {
            return array();
        }

        return $this->_getTagRelationDao()->fetchByTypeIdAndParamIds($typeId, $paramIds);
    }

    /**
     * @return PwTagDao
     */
    protected function _getTagDao()
    {
        return Wekit::loadDao('tag.dao.PwTagDao');
    }

    /**
     * @return PwTagRecordDao
     */
    protected function _getTagRecordDao()
    {
        return Wekit::loadDao('tag.dao.PwTagRecordDao');
    }

    /**
     * @return PwTagRelationDao
     */
    protected function _getTagRelationDao()
    {
        return Wekit::loadDao('tag.dao.PwTagRelationDao');
    }
}
