<?php
/**
 * 话题分类DAO
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 下午03:16:28Z yishuo $
 * @package PwTagCategoryDao
 */
class PwTagCategoryDao extends PwBaseDao
{
    protected $_table = 'tag_category';
    protected $_pk = 'category_id';
    protected $_dataStruct = array('category_id', 'category_name', 'alias', 'vieworder', 'tag_count', 'seo_title', 'seo_description', 'seo_keywords');

    /**
     * 添加一条分类
     *
     * @param  array $data
     * @return int
     */
    public function addTagCategory($data)
    {
        return $this->_add($data);
    }

    /**
     * 删除一条分类
     *
     * @param  int  $categoryId
     * @return bool
     */
    public function delete($categoryId)
    {
        return $this->_delete($categoryId);
    }

    /**
     * 修改一条分类
     *
     * @param  int   $categoryId
     * @param  array $data
     * @return bool
     */
    public function update($categoryId, $data)
    {
        return $this->_update($categoryId, $data);
    }

    /**
     * 批量添加分类
     *
     * @param  array $data
     * @return int
     */
    public function addCategorys($data)
    {
        $array = array();
        foreach ($data as $v) {
            if (!$this->_filterStruct($v) || !$v['category_name']) {
                continue;
            }
            $array[] = array(
                $v['category_name'],
                $v['alias'],
                $v['vieworder'],
            );
        }
        if (!is_array($array) || !count($array)) {
            return false;
        }
        $sql = $this->_bindSql('INSERT INTO %s (`category_name`,`alias`,`vieworder`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 修改多条分类
     *
     * @param  array $data
     * @return int
     */
    public function updateCategorys($data)
    {
        $array = array();
        foreach ($data as $v) {
            if (!$this->_filterStruct($v) || !$v['category_id']) {
                continue;
            }
            $array[] = array(
                $v['category_id'],
                $v['category_name'],
                $v['alias'],
                $v['vieworder'],
            );
        }
        if (!is_array($array) || !count($array)) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s (`category_id`,`category_name`,`alias`,`vieworder`) VALUES %s ', $this->getTable(), $this->sqlMulti($array));

        return $this->getConnection()->execute($sql);
    }

    /**
     * 根据category_id获取话题分类
     *
     *@param int $id
     * @return int
     */
    public function get($id)
    {
        return $this->_get($id);
    }

    /**
     * 获取所有话题分类
     *
     * @return int
     */
    public function getAllCategorys()
    {
        $sql = $this->_bindTable('SELECT * FROM %s ORDER BY `vieworder` ASC');
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'category_id');
    }

    /**
     * 获取所有话题分类
     *
     * @return int
     */
    public function fetchCategories($ids)
    {
        $sql = $this->_bindSql('SELECT * FROM %s WHERE category_id IN %s ORDER BY `vieworder` ASC', $this->getTable(), $this->sqlImplode($ids));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll(array(), 'category_id');
    }
}
