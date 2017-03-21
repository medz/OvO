<?php
/**
 * 话题分类关系DAO.
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 */
class PwTagCategoryRelationDao extends PwBaseDao
{
    protected $_table = 'tag_category_relation';
    protected $_dataStruct = ['tag_id', 'category_id'];

    /**
     * 添加.
     *
     * @param array $data
     *
     * @return int
     */
    public function addRelations($data)
    {
        $array = [];
        foreach ($data as $v) {
            if (! $this->_filterStruct($v)) {
                continue;
            }
            $array[] = [
                $v['tag_id'],
                $v['category_id'],
            ];
        }
        $sql = $this->_bindSql('REPLACE INTO %s (`tag_id`,`category_id`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 根据category_id删除.
     *
     * @param int $categoryId
     *
     * @return bool
     */
    public function deleteByCategoryId($categoryId)
    {
        $sql = $this->_bindTable('DELETE FROM %s  WHERE `category_id`=?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->update([$categoryId]);
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
     * 根据tag_ids删除.
     *
     * @param array $tagIds
     *
     * @return bool
     */
    public function deleteByTagIds($tagIds)
    {
        $sql = $this->_bindSql('DELETE FROM %s  WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 根据category_id获取数据.
     *
     * @param int $categoryId
     * @param int $num
     *
     * @return array
     */
    public function getByCategoryId($categoryId, $num)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `category_id` =? %s ', $this->getTable(), $this->sqlLimit($num));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$categoryId]);
    }

    /**
     * 统计分类话题数.
     *
     * @return array
     */
    public function countByCategoryId()
    {
        $sql = $this->_bindTable('SELECT COUNT(*) as count,category_id FROM %s GROUP BY `category_id`');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([], 'category_id');
    }

    /**
     * 根据$tagId获取数据.
     *
     * @param int $tagId
     *
     * @return array
     */
    public function getByTagId($tagId)
    {
        $sql = $this->_bindTable('SELECT * FROM %s WHERE `tag_id` =?');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$tagId]);
    }

    /**
     * 根据分类id　及　tag_ids获取数据.
     *
     * @param array $tagIds
     * @param int   $categoryId
     *
     * @return array
     */
    public function getByCategoryAndTagIds($tagIds, $categoryId)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `category_id` =? AND `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([$categoryId], 'tag_id');
    }

    /**
     * 根据tag_ids获取数据.
     *
     * @param array $tagIds
     */
    public function getByTagIds($tagIds)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE `tag_id` IN %s ', $this->getTable(), $this->sqlImplode($tagIds));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll([]);
    }
}
