<?php

/**
 * 友情链接DS.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:18:51Z yishuo $
 */
class PwLink
{
    /**
     * 删除一条友情链接.
     *
     * @param int $lid
     *
     * @return bool
     */
    public function deleteLink($lid)
    {
        $lid = intval($lid);
        if ($lid < 1) {
            return false;
        }

        return $this->_getLinkDao()->delete($lid);
    }

    /**
     * 删除多条信息.
     *
     * @param array $lids
     *
     * @return bool
     */
    public function batchDelete($lids)
    {
        if (empty($lids) || !is_array($lids)) {
            return array();
        }

        return $this->_getLinkDao()->batchDelete($lids);
    }

    /**
     * 获取一条链接.
     *
     * @param int $lid
     *
     * @return array
     */
    public function getLink($lid)
    {
        $lid = intval($lid);
        if ($lid < 1) {
            return false;
        }

        return $this->_getLinkDao()->getLink($lid);
    }

    /**
     * 根据分类获取链接.
     *
     * @param int $ifcheck 0 未审核| 1已审核
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getLinks($start, $limit, $ifcheck = 1)
    {
        return $this->_getLinkDao()->getLinks($start, $limit, $ifcheck);
    }

    /**
     * 获取链接数量.
     *
     * @param int $ifcheck 0 未审核| 1已审核
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function countLinks($ifcheck = 0)
    {
        return $this->_getLinkDao()->countLinks($ifcheck);
    }

    /**
     * 根据分类获取链接.
     *
     * @param array $lids
     *
     * @return array
     */
    public function getLinksByLids($lids)
    {
        if (empty($lids) || !is_array($lids)) {
            return array();
        }

        return $this->_getLinkDao()->getLinksByLids($lids);
    }

    /**
     * 添加一条分类.
     *
     * @param array $data
     *
     * @return int|bool
     */
    public function addLinkType($typename, $vieworder)
    {
        if (!$typename) {
            return false;
        }

        return $this->_getLinkTypeDao()->addLinkType(array('typename' => $typename, 'vieworder' => (int) $vieworder));
    }

    /**
     * 删除分类.
     *
     * @param int $typeId
     *
     * @return bool
     */
    public function deleteType($typeId)
    {
        $typeId = intval($typeId);
        if ($typeId < 1) {
            return false;
        }

        return $this->_getLinkTypeDao()->delete($typeId);
    }

    /**
     * 修改多条分类.
     *
     * @param array $data
     *
     * @return bool
     */
    public function updateLinkType($typeId, $name, $vieworder)
    {
        $typeId = intval($typeId);
        $vieworder = intval($vieworder);
        if ($typeId < 1) {
            return false;
        }
        $data = array(
            'typename'  => $name,
            'vieworder' => $vieworder,
        );

        return $this->_getLinkTypeDao()->update($typeId, $data);
    }

    /**
     * 根据名称获取typeid.
     *
     * @param string $typeName
     *
     * @return int
     */
    public function getTypeByName($typeName)
    {
        if (!$typeName) {
            return array();
        }

        return $this->_getLinkTypeDao()->getByName($typeName);
    }

    /**
     * 获取所有分类.
     *
     * @return array
     */
    public function getAllTypes()
    {
        return $this->_getLinkTypeDao()->getAllTypes();
    }

    /**
     * 根据分类typeid获取关系.
     *
     * @param int $typeid
     *
     * @return array
     */
    public function getByTypeId($typeid = null)
    {
        return $this->_getLinkRelationsDao()->getByTypeId($typeid);
    }

    /**
     * 根据链接ID获取关系.
     *
     * @param int $linkId
     *
     * @return array
     */
    public function getRelationsByTypeId($linkId)
    {
        $linkId = intval($linkId);
        if ($linkId < 1) {
            return array();
        }

        return $this->_getLinkRelationsDao()->getByLinkId($linkId);
    }

    /**
     * 根据链接ID列表批量获取该链接和类型关系.
     *
     * @param array $ids
     *
     * @return array
     */
    public function fetchRelationsByLinkid($ids)
    {
        if (empty($ids)) {
            return array();
        }

        return $this->_getLinkRelationsDao()->fetchByLinkId($ids);
    }

    /**
     * 根据lid删除.
     *
     * @param int $lid
     *
     * @return bool
     */
    public function delRelationsByLid($lid)
    {
        $lid = intval($lid);
        if ($lid < 1) {
            return false;
        }

        return $this->_getLinkRelationsDao()->delRelationsByLid($lid);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $lids
     *
     * @return bool|Ambigous <boolean, rowCount, number>
     */
    public function batchDelRelationsByLid($lids)
    {
        if (!$lids) {
            return false;
        }

        return $this->_getLinkRelationsDao()->batchDelRelationsByLid((array) $lids);
    }

    /**
     * 根据typeid删除.
     *
     * @param int $typeid
     *
     * @return bool
     */
    public function delRelationsByTypeid($typeid)
    {
        $typeid = intval($typeid);
        if ($typeid < 1) {
            return false;
        }

        return $this->_getLinkRelationsDao()->delRelationsByTypeid($typeid);
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $lid
     * @param unknown_type $typeId
     *
     * @return bool
     */
    public function addRelation($lid, $typeId)
    {
        $lid = intval($lid);
        $typeId = intval($typeId);
        if ($lid < 1 || $typeId < 1) {
            return false;
        }
        $this->_getLinkRelationsDao()->addLinkRelations(array('lid' => $lid, 'typeid' => $typeId));
    }

    /**
     * 统计分类数量.
     *
     * @return array
     */
    public function countLinkTypes()
    {
        return $this->_getLinkRelationsDao()->countLinkTypes();
    }

    /**
     * 添加友情链接.
     *
     * @param PwLinksDm $dm
     *
     * @return bool
     */
    public function addLink(PwLinkDm $dm)
    {
        if (($result = $dm->beforeAdd()) !== true) {
            return $result;
        }

        return $this->_getLinkDao()->addLink($dm->getData());
    }

    /**
     * 更新友情链接.
     *
     * @param PwLinksDm $dm
     *
     * @return bool
     */
    public function updateLink(PwLinkDm $dm)
    {
        if (($result = $dm->beforeUpdate()) !== true) {
            return $result;
        }

        return $this->_getLinkDao()->updateLink($dm->getLid(), $dm->getData());
    }

    /**
     * 根据条件搜索.
     *
     * @param PwUserSo $vo
     * @param int      $limit 查询条数
     * @param int      $start 开始查询的位置
     *
     * @return array
     */
    public function searchLink(PwLinkSo $vo, $limit = 10, $start = 0)
    {
        return $this->_getLinkSearchDao()->searchLink($vo->getData(), $vo->getOrderby(), $limit, $start);
    }

    /**
     * 根据条件统计
     *
     * @param PwUserSo $vo
     *
     * @return array
     */
    public function countSearchLink(PwLinkSo $vo)
    {
        return $this->_getLinkSearchDao()->countSearchLink($vo->getData());
    }

    /**
     * PwLinkDao.
     *
     * @return PwLinkDao
     */
    protected function _getLinkDao()
    {
        return Wekit::loadDao('link.dao.PwLinkDao');
    }

    /**
     * PwLinkTypeDao.
     *
     * @return PwLinkTypeDao
     */
    protected function _getLinkTypeDao()
    {
        return Wekit::loadDao('link.dao.PwLinkTypeDao');
    }

    /**
     * PwLinkRelationsDao.
     *
     * @return PwLinkRelationsDao
     */
    protected function _getLinkRelationsDao()
    {
        return Wekit::loadDao('link.dao.PwLinkRelationsDao');
    }

    /**
     * PwLinkSearchDao.
     *
     * @return PwLinkSearchDao
     */
    protected function _getLinkSearchDao()
    {
        return Wekit::loadDao('link.dao.PwLinkSearchDao');
    }
}
