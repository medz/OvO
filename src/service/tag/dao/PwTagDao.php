<?php

Wind::import('SRC:library.base.PwBaseDao');

/**
 * 话题DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:53Z yishuo $
 */
class PwTagDao extends PwBaseDao
{
    protected $_table = 'tag';
    protected $_pk = 'tag_id';
    protected $_table_relation = 'tag_category_relation';
    protected $_table_attention = 'tag_attention';
    protected $_table_content_relation = 'tag_relation';
    protected $_dataStruct = array('tag_id', 'parent_tag_id', 'ifhot', 'tag_name', 'tag_logo', 'iflogo', 'excerpt', 'content_count', 'attention_count', 'created_userid', 'seo_title', 'seo_description', 'seo_keywords');

    /**
     * 添加一条话题.
     *
     * @param array $data
     *
     * @return int
     */
    public function addTag($data)
    {
        return $this->_add($data);
    }

    /**
     * 删除一条话题.
     *
     * @param int $tagId
     *
     * @return bool
     */
    public function delete($tagId)
    {
        return $this->_delete($tagId);
    }

    /**
     * 批量删除话题.
     *
     * @param array $tagIds
     *
     * @return bool
     */
    public function batchDelete($tagIds)
    {
        return $this->_batchDelete($tagIds);
    }

    /**
     * 修改一条话题.
     *
     * @param int   $tagId
     * @param array $data
     * @param array $increaseData
     *
     * @return bool
     */
    public function update($tagId, $data = array(), $increaseData = array())
    {
        return $this->_update($tagId, $data, $increaseData);
    }

    /**
     * 批量修改话题.
     *
     * @param array $tagIds
     * @param array $data
     * @param array $increaseData
     *
     * @return bool
     */
    public function batchUpdate($tagIds, $fields, $increaseFields = array())
    {
        return $this->_batchUpdate($tagIds, $fields, $increaseFields);
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
        return $this->_get($tagId);
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
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'tag_id');
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
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `parent_tag_id`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($parentTagId));
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
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `tag_name`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getOne(array($tagName));
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
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_name` IN %s ', $this->getTable(), $this->sqlImplode($tagNames));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'tag_name');
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
        $where = 'WHERE 1';
        $join = '';
        $param = array();
        if ($name) {
            $where .= ' AND t1.`tag_name` LIKE ?';
            $param[] = '%'.$name.'%';
        }
        if ($ifHot >= 0) {
            $where .= ' AND t1.`ifhot` =?';
            $param[] = $ifHot;
        }
        if ($categoryId) {
            $where .= ' AND t2.`category_id` =?';
            $param[] = $categoryId;
            $join = sprintf('LEFT JOIN %s AS t2 USING(`tag_id`) ', $this->getTable($this->_table_relation));
        }
        if ($attentionCountStart != '') {
            $where .= ' AND t1.`attention_count` >=?';
            $param[] = $attentionCountStart;
        }
        if ($attentionCountEnd != '') {
            $where .= ' AND t1.`attention_count` <=?';
            $param[] = $attentionCountEnd;
        }
        if ($contentCountStart != '') {
            $where .= ' AND t1.`content_count` >=?';
            $param[] = $contentCountStart;
        }
        if ($contentCountEnd != '') {
            $where .= ' AND t1.`content_count` <=?';
            $param[] = $contentCountEnd;
        }
        $sql = $this->_bindSql('SELECT COUNT(*) FROM %s AS t1 %s %s', $this->getTable(), $join, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($param);
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
        $where = 'WHERE 1';
        $join = '';
        $param = array();
        if ($name) {
            $where .= ' AND t1.`tag_name` LIKE ?';
            $param[] = '%'.$name.'%';
        }
        if ($ifHot >= 0) {
            $where .= ' AND t1.`ifhot` =?';
            $param[] = $ifHot;
        }
        if ($categoryId) {
            $where .= ' AND t2.`category_id` =?';
            $param[] = $categoryId;
            $join = sprintf('LEFT JOIN %s AS t2 USING(`tag_id`) ', $this->getTable($this->_table_relation));
        }
        if ($attentionCountStart != '') {
            $where .= ' AND t1.`attention_count` >=? ';
            $param[] = $attentionCountStart;
        }
        if ($attentionCountEnd != '') {
            $where .= ' AND t1.`attention_count` <=? ';
            $param[] = $attentionCountEnd;
        }
        if ($contentCountStart != '') {
            $where .= ' AND t1.`content_count` >=? ';
            $param[] = $contentCountStart;
        }
        if ($contentCountEnd != '') {
            $where .= ' AND t1.`content_count` <=? ';
            $param[] = $contentCountEnd;
        }
        $sql = $this->_bindSql('SELECT * FROM %s AS t1 %s %s ORDER BY t1.`tag_id` DESC '.$this->sqlLimit($limit, $start), $this->getTable(), $join, $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($param, 'tag_id');
    }

    /**
     * 获取我关注的话题.
     *
     * @param int $uid
     * @param int $start
     * @param int $limit
     *
     * @return array
     */
    public function getAttentionTag($uid, $start, $limit)
    {
        $sql = $this->_bindSql('SELECT t.* FROM %s AS t LEFT JOIN %s AS a USING(`tag_id`) WHERE a.`uid`=? ORDER BY t.`content_count` DESC %s ', $this->getTable(), $this->getTable($this->_table_attention), $this->sqlLimit($limit, $start));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($uid), 'tag_id');
    }

    /**
     * 根据参数获取相关话题.
     *
     * @param int   $typeId
     * @param array $paramIds
     *
     * @return array
     */
    public function getTagsByParamIds($typeId, $paramIds)
    {
        $sql = $this->_bindSql('SELECT t.*,a.param_id FROM %s AS t RIGHT JOIN %s AS a USING(`tag_id`) WHERE a.`type_id`=? AND a.`param_id` IN %s ORDER BY t.`content_count` DESC', $this->getTable(), $this->getTable($this->_table_content_relation), $this->sqlImplode($paramIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array($typeId));
    }
}
