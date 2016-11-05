<?php
Wind::import('ADMIN:library.AdminBaseController');
Wind::import('SRV:tag.dm.PwTagDm');

/**
 * 话题后台
 *
 * @author jinlong.panjl <jinlong.panjl@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id$
 * @package ManageController
 */

class ManageController extends AdminBaseController {
	
	private $perpage = 10;
	
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$this->setOutput($handlerAdapter->getControllerKey(),'controllerKey');
		$this->setOutput($handlerAdapter->getModuleKey(),'moduleKey');
	}

	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$conditions = array('page','keyword','ifhot','categoryId','minAttention','maxAttention','minContent','maxContent');
		list($page,$keyword,$ifhot,$categoryId,$minAttention,$maxAttention,$minContent,$maxContent) = $this->getInput($conditions);
		$categories = $this->_getTagCateGoryDs()->getAllCategorys();
		$this->setOutput($categories, 'categories');
		//搜索话题
		$page = intval($page);
		$page < 1 && $page = 1;
		strlen($ifhot) or $ifhot = -1;
		list($start, $limit) = Pw::page2limit($page, $this->perpage);
		list($count,$tags) = $this->_getTagService()->getTagByCondition($start, $limit, $keyword, $ifhot, $categoryId, $minAttention, $maxAttention, $minContent, $maxContent);
		$maxPage = ceil($count / $this->perpage);
		if ($page > $maxPage) {
			$page = $maxPage;
			list($start, $limit) = Pw::page2limit($page, $this->perpage);
			list($count,$tags) = $this->_getTagService()->getTagByCondition($start, $limit, $keyword, $ifhot, $categoryId, $minAttention, $maxAttention, $minContent, $maxContent);
		}
		$tags = $this->_buildTagData($tags,$categories);
		$args = array();
		foreach ($conditions as $v) {
			if ($v == 'page') continue;
			$this->setOutput($$v,$v);
			$args[$v] = $$v;
		}
		$this->setOutput($tags, 'tags');
		//pagination
		$this->setOutput($page, 'page');
		$this->setOutput($count, 'count');
		$this->setOutput($args, 'args');
		$this->setOutput($this->perpage, 'perPage');
		//$this->setOutput(ceil($count/$this->perpage), 'totalpage');
	}
	
	public function addAction(){
		$categories = $this->_getTagCateGoryDs()->getAllCategorys();
		$this->setOutput($categories, 'categories');
	}
	
	/**
	 * 
	 * 保存添加的话题
	 */
	public function doaddAction(){
		$tag = $this->getInput('tag');
		if (!$tag['name']) $this->showError('Tag:tagname.empty');
		if ($this->_getTagDs()->getTagByName($tag['name'])) $this->showError("话题{$tag['name']}已存在"); 
		$tag = $this->getInput('tag');
		$logo = $this->uploadLogo();
		$dm = new PwTagDm();
		$dm->setName($tag['name'])
			->setTagLogo($logo)
			->setTypeId(PwTag::TYPE_THREAD_TOPIC)
			->setIfhot(1)
			->setCreateUid($this->loginUser->uid)
			->setExcerpt($tag['excerpt'])
			->setSeoTitle($tag['seo_title'])
			->setSeoDescript($tag['seo_description'])
			->setSeoKeywords($tag['seo_keywords']);
		$logo && $dm->setIflogo(1);
		if (is_numeric($tagId = $this->_getTagDs()->addTag($dm))) {
			// 话题内容关系
			$tag['category'] && $this->_getTagCateGoryDs()->addCategoryRelations($tagId,$tag['category']);
			//关联话题
			if ($tag['relate_tags']) {
				$tagNames = explode(',', $tag['relate_tags']);
				foreach ($tagNames as $v) $this->_addRelateTag($tagId, $v);
			}
			$this->_deleteHotTagCache();
			$this->showMessage('话题添加成功！');
		} else {
			if ($tagId instanceof PwError) {
				$this->showError($tagId->getError());
			}
			$this->showError('话题添加失败！');
		}
	}
	
	/**
	 * 
	 * 编辑话题
	 */
	public function editAction() {
		$tagId = intval($this->getInput('id','get'));
		$tag = $this->_getTagDs()->getTag($tagId);
		$categories = $this->_getTagCateGoryDs()->getAllCategorys();
		//取关联话题
		$relatedTag = $this->_getTagDs()->getTagByParent($tagId);
		if ($relatedTag) {
			$relatedTagHtml = array();
			foreach ($relatedTag as $v) {
				$relatedTagHtml[] = $v['tag_name'];
			}
			$this->setOutput(implode(',', $relatedTagHtml), 'relatedTags');
		}
		//话题分类
		$tagCategories = $this->_getTagCateGoryDs()->getCategoriesByTagId($tagId);
		$tagCategories and $this->setOutput($tagCategories, 'tagCategories');
		$this->setOutput($tag, 'tag');
		$this->setOutput($categories, 'categories');
	}

	/**
	 * 
	 * 保存话题
	 */
	public function doeditAction(){
		$tag = $this->getInput('tag');
		if (!$tag['name']) $this->showError('Tag:tagname.empty');
		$tagInfo = $this->_getTagDs()->getTag($tag['tag_id']);
		if (!$tagInfo) {
			$this->showError('话题不存在！');
		}
		$logo = $this->uploadLogo();
		$dm = new PwTagDm($tag['tag_id']);
		$dm->setName($tag['name'])
			->setExcerpt($tag['excerpt'])
			->setSeoTitle($tag['seo_title'])
			->setSeoDescript($tag['seo_description'])
			->setSeoKeywords($tag['seo_keywords']);
		if ($logo) {
			$dm->setTagLogo($logo)
				->setIflogo(1);
		}
		if ($logo && $logo != $tagInfo['tag_logo']) {
			Pw::deleteAttach($tagInfo['tag_logo']);
		}
		//取消原关联话题
		
		$this->_getTagService()->removeRelatedTopic($tag['tag_id']);
		if ($tag['relate_tags']) {
			$tagInfo = $this->_getTagDs()->getTag($tag['tag_id']);
				$tagInfo['parent_tag_id'] && $this->showError(sprintf('话题"%s"已经有关联话题,不允许再合并关联',$tagInfo['tag_name']));
			$tagNames = explode(',', $tag['relate_tags']);
			foreach ($tagNames as $v) {
				$mergeTag = $this->_getTagDs()->getTagByName($v);
				if ($mergeTag['parent_tag_id']) {
					$parentTag = $this->_getTagDs()->getTag($mergeTag['parent_tag_id']);
					$this->showError(sprintf('话题"%s"的关联话题为%s,不允许再合并关联',$mergeTag['tag_name'],$parentTag['tag_name']));
				}
				$this->_addRelateTag($tag['tag_id'], $v);
			}
		}
		$this->_getTagCateGoryDs()->updateCategoryRelations($tag['tag_id'],$tag['category']);
		$result = $this->_getTagDs()->updateTag($dm);
		if ($result instanceof PwError) {
			$this->showError($result->getError());
		}
		$this->_deleteHotTagCache();
		$this->showMessage('话题编辑成功！');
	}

	/**
	 * 
	 * 合并关联话题
	 */
	public function mergeAction(){
		
	}

	/**
	 * 
	 * 删除话题
	 */
	public function deleteAction(){
		$tagIds = $this->getInput('tag_id', 'post');
		is_array($tagIds) or $tagIds = explode(',', $tagIds);
		if (!$tagIds) $this->showError('operate.select');
		$this->_getTagService()->deleteByTagIds($tagIds);
		$this->_deleteHotTagCache();
		$this->showMessage('话题删除成功！');
	}
	
	/**
	 * 
	 * 取消热门话题
	 */
	public function deletehotAction(){
		$tagIds = $this->getInput('tag_id');
		if (!$tagIds) $this->showError('operate.select');
		foreach ($tagIds as $tagId) {
			$dm = new PwTagDm($tagId);
			$dm->setIfhot(0);
			$this->_getTagDs()->updateTag($dm);
		}
		//删除update表
		$this->_getTagDs()->deleteTagRecords($tagIds);
		$this->_deleteHotTagCache();
		$this->showMessage('ADMIN:success');
	}
	
	/**
	 * 
	 * 设置热门话题
	 */
	public function sethotAction(){
		$tagIds = $this->getInput('tag_id');
		if (!$tagIds) $this->showError('operate.select');
		$time = Pw::getTime();
		foreach ($tagIds as $tagId) {
			$dm = new PwTagDm($tagId);
			$dm->setIfhot(1);
			$this->_getTagDs()->updateTag($dm);
			$this->_getTagDs()->addTagRecord($tagId,$time);
		}
		//删除update表
		$this->_deleteHotTagCache();
		$this->showMessage('ADMIN:success');
	}
	
	/**
	 * 
	 * 合并关联话题
	 */
	public function domergeAction(){
		$tagIds = explode(',', $this->getInput('tag_id', 'post'));
		$tagName = $this->getInput('tag_name', 'post');
		if (!$tagIds || !$tagName) $this->showError('请输入关联话题名称');
		$tag = $this->_getTagDs()->getTagByName($tagName);
		//检查待合并的TAG有无所属话题
		$checkTags = $this->_getTagDs()->fetchTag($tagIds);
		foreach ($checkTags as $v) {
			$parentTag = $this->_getTagDs()->getTag($v['parent_tag_id']);
			$parentTag && $this->showError(sprintf('话题"%s"已经合并到话题"%s",不允许再合并到其他话题',$v['tag_name'],$parentTag['tag_name']));
			$tagInfo = $this->_getTagDs()->getTagByParent($v['tag_id']);
			$tagInfo && $this->showError(sprintf('话题"%s"已经存在合并话题"%s",不允许被合并到其他话题',$v['tag_name'],$tagInfo['tag_name']));
		}
		if (!$tag) {
			$dm = new PwTagDm();
			$dm->setName($tagName);
			$toTagId = $this->_getTagDs()->addTag($dm);
		} else {
			$toTagId = $tag['tag_id'];
			if ($tag['parent_tag_id']) {
				$parentTag = $this->_getTagDs()->getTag($tag['parent_tag_id']);
				$this->showError(sprintf('话题"%s"已经合并到话题"%s",不允许合并其他话题',$tag['tag_name'],$parentTag['tag_name']));
			}
		}
		foreach ($tagIds as $tagId) {
			$this->_mergeTags($tagId,$toTagId);
		}
		$this->_deleteHotTagCache();
		$this->showMessage('合并关联话题成功！');
	}
	
	/**
	 * 
	 * 移动分类
	 */
	public function moveAction(){
		$categories = $this->_getTagCateGoryDs()->getAllCategorys();
		$this->setOutput($categories, 'categories');
	}
	
	/**
	 * 
	 * 移动分类
	 */
	public function domoveAction(){
		$tagIds = explode(',', $this->getInput('tag_id'));
		$categoryIds = $this->getInput('category_ids');
		if (!$tagIds || !$categoryIds) $this->showError('operate.select');
		if (count($categoryIds) > 1) {
			$key = array_search(0, $categoryIds);
			if ($key !== false) unset($categoryIds[$key]); 
		}
		foreach ($tagIds as $tagId) {
			$this->_getTagCateGoryDs()->updateCategoryRelations($tagId,$categoryIds);
		}
		$this->_deleteHotTagCache();
		$this->showMessage('操作成功');
	}
	
	/**
	 * 
	 * 话题分类列表
	 */
	public function categoryAction() {
		$categorys = $this->_getTagCateGoryDs()->getCategorysWithCount();
		$this->setOutput($categorys, 'categorys');
	}

	/**
	 * 
	 * 编辑分类
	 */
	public function editCategoryAction() {
		$id = $this->getInput('id');
		$category = $this->_getTagCateGoryDs()->getCategoryById($id);
		$this->setOutput($category, 'category');
	}

	/**
	 * 
	 * 提交编辑
	 */
	public function doEditCategoryAction() {
		$data = $this->getInput('data');
		$id = (int)$this->getInput('id');
		$result = $this->_checkWork($data['alias']);
		if ($result !== true) {
			$this->showError($result);
		}
		$dm = new PwTagDm($id);
		$dm->setCategoryName($data['category_name'])
			->setCategoryAlias($data['alias'])
			->setVieworder($data['vieworder'])
			->setSeoDescript($data['seo_description'])
			->setSeoKeywords($data['seo_keywords'])
			->setSeoTitle($data['seo_title']);
		$this->_getTagCateGoryDs()->updateTagCategory($dm);
		$this->showMessage('success');
	}

	/**
	 * 
	 * 设置分类列表
	 */
	public function setCategoryAction() {
		list($data,$newdata) = $this->getInput(array('data','newdata'));
		$allCategorys = $this->_getTagCateGoryDs()->getAllCategorys();
		$categorys = array();
		foreach ($allCategorys as $v) {
			$categorys[$v['category_id']] = $v['category_name'];
		}
		if ($data) {
			foreach ($data as $v) {
				unset($categorys[$v['category_id']]);
					$aliasWord = $this->_checkWork($v['alias']);
					if ($aliasWord !== true) {
						$this->showError($aliasWord);
					}
				if (in_array($v['category_name'],$categorys)) continue;
				$categorys[$v['category_id']] = $v['category_name'];
				$dm = new PwTagDm($v['category_id']);
				$dm->setCategoryName($v['category_name'])
					->setCategoryAlias($v['alias'])
					->setVieworder($v['vieworder']);
				if (($result = $this->_getTagCateGoryDs()->updateTagCategory($dm)) instanceof PwError) {
					$this->showError($result->getError());
				}
			}
		}
		if ($newdata) {
			foreach ($newdata as $v) {
					$aliasWord = $this->_checkWork($v['alias']);
					if ($aliasWord !== true) {
						$this->showError($aliasWord);
					}
				if (in_array($v['category_name'],$categorys)) continue;
				$dm = new PwTagDm();
				$dm->setCategoryName($v['category_name'])
					->setCategoryAlias($v['alias'])
					->setVieworder($v['vieworder']);
				if (($result = $this->_getTagCateGoryDs()->addTagCategory($dm)) instanceof PwError) {
					$this->showError($result->getError());
				}
			}
		}
		$this->showMessage('success');
	}

	public function deleteCategoryAction() {
		$id = $this->getInput('id', 'post');
		if (!$id) {
			$this->showError('operate.fail');
		}
		$this->_getTagCateGoryDs()->deleteCategory($id);
		$this->showMessage('success');
	}
	
	private function _mergeTags($fromTagId,$toTagId){
		$fromTag = $this->_getTagDs()->getTag($fromTagId);
		if ($fromTag['parent_tag_id']) return false;
		//from
		$dm = new PwTagDm($fromTagId);
		$dm->setParent($toTagId)
			->setContentCount(0)
			->setAttentionCount(0);
		$this->_getTagDs()->updateTag($dm);
		//to
		$dm = new PwTagDm($toTagId);
		$dm->addContentCount($fromTag['content_count'])
			->addAttentionCount($fromTag['attention_count']);
		$this->_getTagDs()->updateTag($dm);
		//update content relation
		$this->_getTagDs()->updateTagRelationByTagId($fromTagId,$toTagId);
		//update update log
		$this->_getTagDs()->updateTagRecordByTagId($fromTagId,$toTagId);
	}
	
	private function _deleteHotTagCache() {
		$allCategorys = $this->_getTagCateGoryDs()->getAllCategorys();
		$keys = array('hot_tags_0');
		foreach ($allCategorys as $v) {
			$keys[] = sprintf('hot_tags_%s',$v['category_id']);
		}
		Wekit::cache()->batchDelete($keys);
	}
	
	private function _checkWork($str) {
		if (!$str) return true;
		if (0 >= preg_match('/^[A-Za-z]+$/', $str)) {
			return 'TAG:charset.error';
		}
		return true;
	}
	
	/**
	 * 添加关联话题
	 * 
	 * @param int $tagId 关联到哪个话题Id
	 * @param string $tagName 话题名称
	 */
	private function _addRelateTag($tagId,$tagName){
		$tagId = intval($tagId);
		if ($tagId < 1 || !$tagName) return false;
		$tag = $this->_getTagDs()->getTag($tagId);
		if (!$tag) return false;
		$relateTag = $this->_getTagDs()->getTagByName($tagName);
		if (!$relateTag) {
			$dm = new PwTagDm();
			$dm->setName($tagName)
				->setCreateUid($this->loginUser->uid)
				->setParent($tagId);
			$this->_getTagDs()->addTag($dm);
		} else {
			if ($relateTag['tag_id'] == $tagId) return false;
			//检查被关联的话题是否存在子话题
			if ($this->_getTagDs()->getTagByParent($relateTag['tag_id'])){
				return false;
			}
			$dm = new PwTagDm($relateTag['tag_id']);
			$dm->setParent($tagId);
			$this->_getTagDs()->updateTag($dm);
		}
		return true;
	}
	
	/**
	 * 
	 * 组装tags数据
	 * @param array $tags
	 */
	private function _buildTagData($tags,$categories){
		if (!is_array($tags)) return array();
		foreach($tags as $k=>$v){
			if (!$v['categories']) continue;
			$tmpCategoryNames = array();
			foreach ($v['categories'] as $v2) {
				$tmpCategoryNames[] = $categories[$v2]['category_name'];
			}
			$tags[$k]['categories'] = implode(',',$tmpCategoryNames);
		}
		return $tags;
	}
	
	/**
	 * 上传话题图标
	 *
	 * @return string
	 */
	private function uploadLogo() {
		Wind::import("SRV:upload.action.PwTagUpload");
		Wind::import('LIB:upload.PwUpload');
		$tagUpload = new PwTagUpload(156, 156);
		$upload = new PwUpload($tagUpload);
		if (($result = $upload->check()) === true) {
			$result = $upload->execute();
		}
		if ($result !== true) {
			$this->showError($result->getError());
		}
		return $tagUpload->getPath();
	}
	
	/**
	 * @return PwTagService
	 */
	private function _getTagService() {
		return Wekit::load('tag.srv.PwTagService');
	}
	
	/**
	 * @return PwTag
	 */
	private function _getTagDs() {
		return Wekit::load('tag.PwTag');
	}
	
	/**
	 * @return PwTagAttention
	 */
	private function _getTagAttentionDs() {
		return Wekit::load('tag.PwTagAttention');
	}
	
	/**
	 * 分类DS
	 * 
	 * @return PwTagCateGory
	 */
	private function _getTagCateGoryDs(){
		return Wekit::load('tag.PwTagCateGory');
	}
}