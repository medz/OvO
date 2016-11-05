<?php
/**
 * 话题分类DS
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package PwTag
 */
class PwTagCateGory {
	
	/**
	 * 添加分类关系 一个话题多个分类
	 *
	 * @param int $tagId
	 * @param array $cateGoryIds
	 * return bool
	 */
	public function addCategoryRelations($tagId,$cateGoryIds) {
		$tagId = intval($tagId);
		if ($tagId < 1 || !is_array($cateGoryIds) || !count($cateGoryIds)) return false;
		$relationsdata = array();
		foreach ($cateGoryIds as $id) {
			$id = intval($id);
			if (!$id) return false;
			$array['category_id'] = $id;
			$array['tag_id'] = $tagId;
			$relationsdata[] = $array;
		}
		return $this->_getTagCategoryRelationDao()->addRelations($relationsdata);
	}
	
	/**
	 * 更新分类关系
	 *
	 * @param $dms
	 * return bool
	 */
	public function updateCategoryRelations($tagId,$cateGoryIds) {
		$tagId = intval($tagId);
		if ($tagId < 1) return false;
		$this->_getTagCategoryRelationDao()->deleteByTagId($tagId);
		return $this->addCategoryRelations($tagId,$cateGoryIds);
	}
	
	/**
	 * 删除分类
	 *
	 * @param int $categoryId
	 * @return bool
	 */
	public function deleteCategory($categoryId) {
		$categoryId = intval($categoryId);
		if ($categoryId < 1) return false;
		$this->_getTagCategoryRelationDao()->deleteByCategoryId($categoryId);
		$this->_getTagCategoryDao()->delete($categoryId);
		return true;
	}
	
	/**
	 * 获取话题分类
	 *
	 * @param int $tagId
	 * @return array
	 */
	public function getCategoriesByTagId($tagId) {
		$tagId = intval($tagId);
		if ($tagId < 1) return array();
		$relations = $this->_getTagCategoryRelationDao()->getByTagId($tagId);
		if (!$relations) return array();
		$categoryIds = array();
		foreach ($relations as $v){
			$categoryIds[] = $v['category_id'];
		}
		return $this->_getTagCategoryDao()->fetchCategories($categoryIds);
	}
	
	/**
	 * 更新一条分类
	 *
	 * @param PwTagDm $dm 
	 * return bool
	 */
	public function updateTagCategory(PwTagDm $dm){
		if (!$dm->getField('category_name')) {
			return new PwError('TAG:category.name.empty');
		}
		return $this->_getTagCategoryDao()->update($dm->tag_id, $dm->getData());
	}
	
	/**
	 * 批量添加分类
	 *
	 * @param array $data
	 * @return int
	 */
	public function addTagCategory(PwTagDm $dm) {
		if (!$dm->getField('category_name')) {
			return new PwError('TAG:category.name.empty');
		}
		return $this->_getTagCategoryDao()->addTagCategory($dm->getData());
	}
	
	/**
	 * 获取单条分类
	 *
	 * @return int
	 */
	public function getCategoryById($id) {
		$id = intval($id);
		if ($id < 1) return array();
		return $this->_getTagCategoryDao()->get($id);
	}
	
	/**
	 * 获取所有话题分类
	 *
	 * @return int
	 */
	public function getAllCategorys() {
		return $this->_getTagCategoryDao()->getAllCategorys();
	}
	
	/**
	 * 统计分类话题数 (只提供后台使用)
	 *
	 * @return array
	 */
	public function getCategorysWithCount() {
		$categorys = $this->getAllCategorys();
		$countTags = $this->_getTagCategoryRelationDao()->countByCategoryId();
		$array = array();
		foreach ($categorys as $k => $v) {
			$v['tag_count'] = intval($countTags[$k]['count']);
			$array[$k] = $v;
		}
		return $array;
	}
	
	/**
	 * 删除内容关系数据表
	 * 
	 * @param array $tagIds
	 */
	public function deleteCateGoryRelations($tagIds){
		if (!is_array($tagIds) || !count($tagIds)) {
			return false;
		}
		return $this->_getTagCategoryRelationDao()->deleteByTagIds($tagIds);
	}
	
	/**
	 * 根据tag_ids获取数据
	 * 
	 * @param array $tagIds
	 */
	public function getRelationsByTagIds($tagIds){
		if (!is_array($tagIds) || !count($tagIds)) {
			return false;
		}
		return $this->_getTagCategoryRelationDao()->getByTagIds($tagIds);
	}
	
	/**
	 * @return PwTagCategoryDao
	 */
	protected function _getTagCategoryDao() {
		return Wekit::loadDao('tag.dao.PwTagCategoryDao');
	}
	
	/**
	 * @return PwTagCategoryRelationDao
	 */
	protected function _getTagCategoryRelationDao() {
		return Wekit::loadDao('tag.dao.PwTagCategoryRelationDao');
	}
}