<?php
Wind::import('SRV:tag.dm.PwTagDm');
/**
 * 话题业务
 *
 * @author peihong <peihong.zhangph@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTagService.php 3833 2012-02-16 03:32:27Z peihong.zhangph $
 * @package src.service.tag.srv
 */
class PwTagService {

	private $expireDay = 7;
	
	/**
	 * 批量添加话题
	 *
	 * @param array $dmArray
	 */
	public function addTags($dmArray) {
		if(!is_array($dmArray) || !$dmArray) return false;
		$_tagsInfo = $this->_getTagDs()->getTagsByNames(array_keys($dmArray));
		$tagsKeys = $tagsInfo = array();
		foreach ($_tagsInfo as $k => $tag){
		    $tagsKeys[] = strtolower($k);
		    $k = strtolower($k);
		    $tagsInfo[$k] = $tag;
		}
		$dmArrays = array();
		foreach ($dmArray as $k => $dm){
			$k = strtolower($k);
		    $dmArrays[$k] = $dm;
		}
		$tagRecords = $updateTagDms = $relationDms = $attentionTags = array();
		foreach ($dmArrays as $k => $dm) {
			$k = strtolower(trim($k));
			if (!$k || !$dm instanceof PwTagDm) continue;
			$time = Pw::getTime();
			$dm->setCreatedTime($time);
			$dm->setName($k);
			if (in_array($k, $tagsKeys)) {
				$dm->tag_id = $tagsInfo[$k]['parent_tag_id'] ? $tagsInfo[$k]['parent_tag_id'] : $tagsInfo[$k]['tag_id'];
				$dm->setContentTagId($tagsInfo[$k]['tag_id']);
				$dm->setIfhot($tagsInfo[$k]['ifhot']);
				$updateTagDm = new PwTagDm($dm->tag_id);
				$updateTagDm->addContentCount(1);
				$updateTagDms[] = $updateTagDm;
			} else {
				$dm->setContentCount(1);
				$result = $this->_getTagDs()->addTag($dm);
				if ($result instanceof PwError) {
					return $result;
				}
				$dm->tag_id = $result;
				$dm->setContentTagId($dm->tag_id);
			}
			if ($dm->getIfhot()) {
				$tagRecords[] = array('tag_id' => $dm->tag_id, 'update_time' => $time);
			}
			$relationDms[] = $dm;
		//	$this->addAttention($dm->getCreateUid(),$dm->tag_id);
		}
		
		$this->_getTagDs()->batchAddRelation($relationDms);
		$this->_getTagDs()->batchAddTagRecord($tagRecords);
		$updateTagDms && $this->_getTagDs()->batchUpdate($updateTagDms);
		return $dm->tag_id;
	}
	
	/**
	 * 批量更新帖子话题
	 *
	 * @param int $typeId
	 * @param int $paramId
	 * @param array $dmArray
	 */
	public function updateTags($typeId,$paramId,$dmArray) {
		if (!$typeId || !$paramId) {
			return new PwError('data.error');
		}
		$tagsInfo = $this->_getTagDs()->getTagRelationByType($typeId,$paramId);
		$this->_getTagDs()->batchDeleteRelationsByType($typeId,$paramId,array_keys($tagsInfo));
		$dmArray && $this->addTags($dmArray);
		$types = $this->_getTypeMap();
		$tags = $this->getTagByType($types[$typeId],$paramId);
		Wind::import('SRV:forum.dm.PwTopicDm');
		$dm = new PwTopicDm($paramId);
		$dm->setTags($this->_formatTags($tags));
		Wekit::load('forum.PwThread')->updateThread($dm,PwThread::FETCH_CONTENT);
		return true;
	}
	
	/**
	 * 批量将话题parent_id置0
	 *
	 * @param array $tagIds
	 * @return bool
	 */
	public function clearTagsByParentIds($tagIds){
		if (!is_array($tagIds) || !count($tagIds)) return false;
		Wind::import('SRV:tag.dm.PwTagDm');
		$dm = new PwTagDm();
		$dm->setParent(0);
		return $this->_getTagDs()->updateTags($tagIds,$dm);
	}
	
	/**
	 * 
	 * 根据类型名获取最新
	 * @param int $tagId
	 * @param string $typeName
	 * @param int $num
	 */
	public function getContentsByTypeName($tagId,$typeName,$ifcheck,$offset=0,$num = 4){
		$tagId = intval($tagId);
		$num = intval($num);
		$typeId = $this->getTypeIdByTypeName($typeName);
		$relations = $this->_getTagDs()->getTagRelation($tagId,$typeId,$ifcheck,$offset,$num);
		$ids = $array = $return = array();
		foreach ($relations as $v) {
			$array[$v['param_id']] = $v;
			$ids[] = $v['param_id'];
		}
		if (!$ids) return array();
		$action = $this->_getTagAction($typeName);
		if (!$action) return new PwError('undefined content type');
		$result = $action->getContents($ids);
		foreach ($ids as $id) {
			$result[$id] && $result[$id]['tagifcheck'] = $array[$id]['ifcheck'];
			$return[$id] = $result[$id];
		}
		usort($return, array($this, 'cmp'));
		return $return;
	}
	
	private static function cmp($a, $b) {
		    return strcmp($b["created_time"], $a["created_time"]);
	}
	
	public function getHotTags($categoryId = 0,$num = 100) {
		return Wekit::cache()->get('hot_tags', array($categoryId, $num));
	}
	
	/**
	 * 
	 * 获取热门话题
	 * @param ing $categoryId
	 * @param ing $num
	 */
	public function getHotTagsNoCache($categoryId = 0,$num = 100){
		// 删除过期数据
		$updateTime = pw::getTime() - 86400 * $this->expireDay;
		$this->_getTagDs()->deleteExpireHotTag($updateTime);
		$tags = $this->_getTagDs()->getCountHotTag($categoryId,$num);
		$tagIds = array_keys($tags);
		if (!$tagIds) return array();
		return $this->_getTagDs()->fetchTag($tagIds);
	}
	
	/**
	 * 
	 * 获取内容的其它话题
	 * @param string $typeName
	 * @param array $params 内容参数ID
	 * @param array $excludeTagIds 需排除的当前话题列表 format: array(tag_id_param_id,..);
	 */
	public function getRelatedTags($typeName,$params,$excludeTagIds = array()){
		$relatedTags = array();
		$params = array_unique($params);
		$typeId = $this->getTypeIdByTypeName($typeName);
		$params and $tmpRelatedTags = $this->_getTagDs()->getTagsByParamIds($typeId,$params);
		foreach ($tmpRelatedTags as $v) {
			$tmpTagId = $v['tag_id'];
			$tmpParamId = $v['param_id'];
			//if (in_array("{$tmpTagId}_$tmpParamId", $excludeTagIds)) continue;
			$relatedTags[$tmpParamId][$tmpTagId] = $v;
		}
		return $relatedTags;
	}
	
	/**
	 * 
	 * 获取关注会员
	 * @param unknown_type $tagId
	 */
	public function getTagMembers($tagId,$offset,$num = 20){
		$count = $this->_getTagAttentionDs()->countAttentionByTagId($tagId);
		if ($count < 1) {
			return array(0,array());
		}
		$attentions = $this->_getTagAttentionDs()->getAttentionUids($tagId,$offset,$num);
		$users = $this->_getUserDs()->fetchUserByUid(array_keys($attentions));
		return array($count,$users);
	}
	
	/**
	 * 获取我关注的话题
	 *
	 * @param int $uid
	 * @param int $start
	 * @param int $limit
	 * @return array 
	 */
	public function getAttentionTags($uid,$start,$limit) {
		$uid = intval($uid);
		$count = $this->_getTagAttentionDs()->countAttentionByUid($uid);
		if ($count < 1) {
			return array(0,array());
		}
		$relations = $this->_getTagDs()->getAttentionByUid($uid,$start,$limit);
		$tags = $this->_getTagDs()->fetchTag(array_keys($relations));
		return array($count,$tags);
	}
	
	/**
	 * 根据应用类型和id获取话题
	 *
	 * @param string $type
	 * @param int $paramId
	 * @param int $uid 带关注
	 * @return array 
	 */
	public function getTagByType($type,$paramId) {
		$paramId = intval($paramId);
		if (!$type || $paramId < 1) {
			return array();
		}
		$typeId = $this->getTypeIdByTypeName($type);
		if (!$typeId) return array();
		$tagRelations = $this->_getTagDs()->getTagRelationByType($typeId,$paramId);
		if (!count($tagRelations)) return array();
		$tagIds = array_keys($tagRelations);
		return $this->_getTagDs()->fetchTag($tagIds);
	}
	
	/**
	 * 话题小名片
	 *
	 * @param string $name
	 * @param int $uid 带关注
	 * @return array 
	 */
	public function getTagCard($name,$uid = null) {
		$tag = $this->_getTagDs()->getTagByName($name);
		if (!$tag) return array();
		if ($uid) {
			$attention = $this->_getTagAttentionDs()->isAttentioned($uid,$tag['tag_id']);
		}
		$attention && $tag['isAttention'] = $attention ? true : false;
		return $tag;
	}
	
	/**
	 * 
	 * 根据类型名获取类型ID
	 * @param string $typeName
	 */
	public function getTypeIdByTypeName($typeName){
		$types = array_flip($this->_getTypeMap());
		return $types[$typeName];
	}
	
	/**
	 * 关注话题
	 *
	 * @param int $uid
	 * @param int $tagId
	 * @return array 
	 */
	public function addAttention($uid,$tagId) {
		// 是否关注过了
		if ($this->_getTagAttentionDs()->isAttentioned($uid,$tagId)) return new PwError('Tag:have.attentioned');
		if (($count = $this->_getTagAttentionDs()->countAttentionByUid($uid)) > 49) {
			return new PwError('Tag:attentioned.count.error');
		}
		$result = (int)$this->_getTagAttentionDs()->addAttention($uid,$tagId);
		// 更新话题表内容数
		Wind::import('SRV:tag.dm.PwTagDm');
		$dm = new PwTagDm($tagId);
		$dm->addAttentionCount($result);
		return $this->_getTagDs()->updateTag($dm);
	}
	
	/**
	 * 取消关注的话题
	 *
	 * @param int $uid
	 * @param int $tagId
	 * @return array 
	 */
	public function deleteAttention($uid,$tagId) {
		$result = (int)$this->_getTagAttentionDs()->deleteAttention($uid,$tagId);
		// 更新话题表内容数
		Wind::import('SRV:tag.dm.PwTagDm');
		$dm = new PwTagDm($tagId);
		$dm->addAttentionCount(-$result);
		return $this->_getTagDs()->updateTag($dm);
	}
	
	/**
	 * 批量删除话题 -- 只供管理话题删除接口
	 *
	 * @param array $tagIds
	 * @return bool
	 */
	public function deleteByTagIds($tagIds) {
		$result = $this->_getTagDs()->fetchTag($tagIds);
		if (!$result) return false;
		foreach ($result as $tag) {
			$tag['tag_logo'] && Pw::deleteAttach($tag['tag_logo']);
		}
		$tagIds = array_keys($result);
		// 删除热门话题排行
		$this->_getTagDs()->deleteTagRecords($tagIds);
		// 删除分类关系
		$this->_getTagCateGoryDs()->deleteCateGoryRelations($tagIds);
		// 删除关注
		$this->_getTagAttentionDs()->deleteAttentions($tagIds);
		// 删除内容关系
		$this->_getTagDs()->deleteRelations($tagIds);
		$this->clearTagsByParentIds($tagIds);//TODO
		// 删除话题
		$this->_getTagDs()->batchDelete($tagIds);
		return true;
	}

	/**
	 * 
	 * 取消某话题的关联话题
	 * @param int $tagId
	 */
	public function removeRelatedTopic($tagId){
		$tagId = intval($tagId);
		$childTags = $this->_getTagDs()->getTagByParent($tagId);
		if (!$childTags) return true;
		$childTagIds = array();
		foreach ($childTags as $tag){
			$childTagIds[] = $tag['tag_id'];
			$this->_getTagDs()->updateTagRelationByTagId($tagId,$tag['tag_id']);
		}
		
		Wind::import('SRV:tag.dm.PwTagDm');
		$dm = new PwTagDm();
		$dm->setParent(0);
		$this->_getTagDs()->updateTags($childTagIds,$dm);
	}
	
	/**
	 * 搜索话题列表
	 * 
	 * @param int $start
	 * @param int $limit
	 * @param string $name
	 * @param int $ifHot
	 * @param int $categoryId
	 * @param int $attentionCountStart
	 * @param int $attentionCountEnd
	 * @param int $contentCountStart
	 * @param int $contentCountEnd
	 * @return array 
	 */
	public function getTagByCondition($start,$limit,$name,$ifHot,$categoryId,$attentionCountStart,$attentionCountEnd,$contentCountStart,$contentCountEnd) {
		$count = $this->_getTagDs()->countTagByCondition($name,$ifHot,$categoryId,$attentionCountStart,$attentionCountEnd,$contentCountStart,$contentCountEnd);
		if ($count < 1) return array(0,array());
		$tags = $this->_getTagDs()->getTagByCondition($start,$limit,$name,$ifHot,$categoryId,$attentionCountStart,$attentionCountEnd,$contentCountStart,$contentCountEnd);
		//取话题分类关系
		$categoryRelations = $this->_getTagCateGoryDs()->getRelationsByTagIds(array_keys($tags));
		$tmpCategories = array();
		foreach ($categoryRelations as $l) {
			$tmpCategories[$l['tag_id']][] = $l['category_id'];
		}
		foreach ($tags as $k => $v) {
			$v['parent_tag_id'] or $v['joinTag'] = $this->_getTagDs()->getTagByParent($k);
			$v['categories'] = $tmpCategories[$k];
			$tags[$k] = $v;
		}
		return array($count,$tags);
	}
	
	/**
	 * 批量删除内容关系
	 *
	 * @param string $type
	 * @param array $paramIds
	 * @return bool
	 */
	public function batchDeleteRelation($typeId,$paramIds) {
		return $this->_getTagDs()->batchDeleteRelation($typeId,$paramIds);
	}
	
	/**
	 * 解析话题 #话题#
	 * 
	 * @param string $content
	 * @return string
	 */
	public function parserTags($content) {
		if (!$content) return array();
		preg_match_all('/\#(.*)\#/iUs', $content, $matches);
		if (!$matches[1]) return array();
		$tags = array();
		foreach ($matches[1] as $v) {
			$v = trim($v);
			if (!$v) continue;
			$tags[] = $v;
		}
		return $tags;
	}
	
	/**
	 * 
	 * 获取类型ID和类型名的对应关系
	 * @return array
	 */
	private function _getTypeMap(){
		return $this->_getTagDs()->typeMap;
	}
	
	/**
	 * 
	 * 获取Tag实现方法
	 * @param string $typeName
	 */
	private function _getTagAction($typeName){
		$typeName = strtolower($typeName);
		if (!$this->getTypeIdByTypeName($typeName)) return null;
		$className = 'PwTag' . ucfirst($typeName);
		Wind::import('SRV:tag.srv.action.' . $className);
		return new $className;
	}
	
	protected function _formatTags($tags) {
		if (!$tags) return false;
		$tagname = array();
		foreach ($tags as $v) {
			$tagname[] = $v['tag_name'];
		}
		return implode(',',$tagname);
	}
	
	private function _getCacheService(){
		Wind::import("Lib:utility.PwCacheService");
		return new PwCacheService();
	}
	
	/**
	 * 话题DS
	 * 
	 * @return PwTag
	 */
	private function _getTagDs(){
		return Wekit::load('tag.PwTag');
	}
	
	/**
	 * 分类DS
	 * 
	 * @return PwTagCateGory
	 */
	private function _getTagCateGoryDs(){
		return Wekit::load('tag.PwTagCateGory');
	}
	
	/**
	 * 关注DS
	 * 
	 * @return PwTagAttention
	 */
	private function _getTagAttentionDs(){
		return Wekit::load('tag.PwTagAttention');
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return PwUser
	 */
	private function _getUserDs(){
		return Wekit::load('user.PwUser');
	}
}