<?php
/**
 * 话题容关系DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 */
class PwTagRelationDao extends PwBaseDao
{
    protected $_table = 'tag_relation';
    protected $_dataStruct = ['tag_id', 'content_tag_id', 'type_id', 'param_id', 'ifcheck', 'created_time'];

    /**
     * 单个添加内容关系.
     *
     * @param array $data
     *
     * @return int
     */
    public function addRelation($data)
    {
        return $this->_add($data, false);
    }

    /**
     * 批量添加.
     *
     * @param array $data
     *
     * @return int
     */
    public function batchAddRelation($data)
    {
        $array = [];
        foreach ($data as $v) {
            if (!$this->_filterStruct($v)) {
                continue;
            }
            $array[] = [
                $v['tag_id'],
                $v['content_tag_id'],
                $v['type_id'],
                $v['param_id'],
                $v['created_time'],
            ];
        }
        $sql = $this->_bindSql('INSERT INTO %s (`tag_id`,`content_tag_id`,`type_id`,`param_id`,`created_time`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 更新内容关系.
     *
     * @param array $data
     *
     * @return int
     */
    public function updateRelation($typeId, $paramId, $id, $data)
    {
        if (!$data = $this->_filterStruct($data)) {
            return false;
        }
        $sql = $this->_bindSql('UPDATE %s SET %s WHERE `type_id`=? AND `param_id`=? AND `content_tag_id`=?', $this->getTable(), $this->sqlSingle($data));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$typeId, $paramId, $id]);
    }

    /**
     * 批量添加内容关系.
     *
     * @param array $data
     *
     * @return int
     */
    public function addRelations($data)
    {
        $array = [];
        foreach ($data as $v) {
            if (!$this->_filterStruct($v)) {
                continue;
            }
            $array[] = [
                $v['tag_id'],
                $v['content_tag_id'],
                $v['type_id'],
                $v['param_id'],
                $v['ifcheck'],
                $v['created_time'],
            ];
        }
        $sql = $this->_bindSql('REPLACE INTO %s (`tag_id`,`content_tag_id`,`type_id`,`param_id`,`ifcheck`,`created_time`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 更新tag relation表的tagid,content id.
     *
     * @param int $fromTagId
     * @param int $toTagId
     *
     * @return bool
     */
    public function updateTagRelationByTagId($fromTagId, $toTagId)
    {
        $sql = $this->_bindTable('UPDATE %s SET tag_id=? WHERE `tag_id`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$toTagId, $fromTagId]);
    }

    /**
     * 根据tag_id删除.
     *
     * @param int $tagId
     *
     * @return bool
     */
    public function deleteByTagId($tagId)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE `tag_id`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$tagId]);
    }

    /**
     * 根据tag_ids批量删除.
     *
     * @param array $tagIds
     *
     * @return bool
     */
    public function deleteByTagIds($tagIds)
    {
        $sql = $this->_bindSql('DELETE FROM %s  WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([]);
    }

    /**
     * 根据类型和ID删除.
     *
     * @param int $typeId
     * @param int $paramId
     *
     * @return bool
     */
    public function deleteByTypeId($typeId, $paramId)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE `type_id`=? AND `param_id` =?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$typeId, $paramId], true);
    }

    /**
     * 根据type_id、param_id、content_tag_id删除一条
     *
     * @param int $typeId
     * @param int $paramId
     * @param int $tagId
     *
     * @return bool
     */
    public function delete($typeId, $paramId, $tagId)
    {
        $sqlAdd = ' WHERE `type_id`=?';
        $param = [$typeId];
        if ($paramId) {
            $sqlAdd .= ' AND `param_id` =?';
            $param[] = $paramId;
        }
        if ($tagId) {
            $sqlAdd .= ' AND `content_tag_id` =?';
            $param[] = $tagId;
        }
        $sql = $this->_bindSql('DELETE FROM %s %s ', $this->getTable(), $sqlAdd);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update($param, true);
    }

    /**
     * 根据type_id、param_id、content_tag_ids批量删除.
     *
     * @param int   $typeId
     * @param int   $paramId
     * @param array $tagIds
     *
     * @return bool
     */
    public function batchDeleteRelationsByType($typeId, $paramId, $tagIds)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE `type_id`=? AND `param_id` =? AND `content_tag_id` IN %s', $this->getTable(), $this->sqlImplode($tagIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$typeId, $paramId]);
    }

    /**
     * 根据type_id、param_ids批量删除.
     *
     * @param int   $typeId
     * @param array $paramIds
     *
     * @return bool
     */
    public function batchDelete($typeId, $paramIds)
    {
        $sql = $this->_bindSql('DELETE FROM %s WHERE `type_id`=? AND `param_id` IN %s ', $this->getTable(), $this->sqlImplode($paramIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->execute([$typeId]);
    }

    /**
     * 根据类型和ID获取数据.
     *
     * @param int $typeId
     * @param int $paramId
     *
     * @return array
     */
    public function getByTypeId($typeId, $paramId)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `type_id`=? AND `param_id` =?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$typeId, $paramId], 'content_tag_id');
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
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `type_id`=? AND `param_id` IN %s', $this->getTable(), $this->sqlImplode($paramIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$typeId], 'content_tag_id');
    }

    /**
     * 根据类型和ID统计数据.
     *
     * @param int $tagId
     * @param int $typeId
     * @param int $ifcheck
     *
     * @return array
     */
    public function countByTagId($tagId, $typeId, $ifcheck)
    {
        $param = [$tagId, $typeId];
        $where = 'WHERE `tag_id` =? AND `type_id`=?';
        if ($ifcheck) {
            $where .= ' AND `ifcheck` =? ';
            $param[] = $ifcheck;
        }
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s ', $this->getTable(), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($param);
    }

    /**
     * 根据类型和ID获取数据.
     *
     * @param int $tagId
     * @param int $typeId
     * @param int $ifcheck
     *
     * @return array
     */
    public function getByTagId($tagId, $typeId, $ifcheck, $offset, $num = 4)
    {
        $param = [$tagId, $typeId];
        $where = 'WHERE `tag_id` =? AND `type_id`=?';
        if ($ifcheck) {
            $where .= ' AND `ifcheck` =? ';
            $param[] = $ifcheck;
        }
        $sql = $this->_bindSql('SELECT * FROM %s %s ORDER BY `created_time` DESC %s', $this->getTable(), $where, $this->sqlLimit($num, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($param, 'param_id');
    }
}
