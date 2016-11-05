<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwEmotion.php 6800 2012-03-26 09:37:53Z gao.wanggao $ 
 * @package 
 */

class PwEmotionCategory {
	
	/**
	 * 获取一条分类信息
	 * 
	 * @param int $categoryId
	 */
	public function getCategory($categoryId) {
		$categoryId = (int)$categoryId;
		if ($categoryId < 1) return array();
		return $this->_getDao()->getCategory($categoryId);
	}
	
	/**
	 * 获取多条分类信息
	 * 
	 * @param array $categoryIds
	 */
	public function fetchCategory($categoryIds) {
		if (!is_array($categoryIds) || !$categoryIds) return array();
		return $this->_getDao()->fetchCategory($categoryIds);
	}
	
	/**
	 * 获取分类列表
	 *
	 * @param string $app
	 * @param bool $isOpen
	 */
	public function getCategoryList($app = '', $isOpen = null) {
		isset($isOpen) && $isOpen = (int)$isOpen;
		return $this->_getDao()->getCategoryList($app, $isOpen);
	}
	
	public function addCategory(PwEmotionCategoryDm $dm) {
		$resource=$dm->beforeAdd();
		if ($resource instanceof PwError) return $resource;
		return $this->_getDao()->addCategory($dm->getData());
	}
	
	public function updateCategory(PwEmotionCategoryDm $dm) {
		$resource=$dm->beforeUpdate();
		if ($resource instanceof PwError) return $resource;
		return $this->_getDao()->updateCategory($dm->categoryId, $dm->getData());
	}
	
	public function deleteCategory($categoryId) {
		$categoryId = (int)$categoryId;
		if ($categoryId < 1) return false;
		return $this->_getDao()->deleteCategory($categoryId);
	}
	
	private function _getDao() {
		return Wekit::loadDao('emotion.dao.PwEmotionCategoryDao');
	}
}
?>