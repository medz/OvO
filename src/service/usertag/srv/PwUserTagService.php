<?php
Wind::import('SRV:usertag.dm.PwUserTagDm');

/**
 * 个人标签的service 
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserTagService.php 15027 2012-07-30 09:37:09Z xiaoxia.xuxx $
 * @package usertag.srv
 */
class PwUserTagService {
	
	/**
	 * 根据用户ID获得用户的标签列表
	 *
	 * @param int $uid
	 * @return array
	 */
	public function getUserTagList($uid) {
		if ($uid < 1) return array();
		$relations = $this->_getTagRelationDs()->getRelationByUid($uid);
		return $this->_getTagDs()->fetchTag(array_keys($relations));
	}
	
	/**
	 * 添加一个用户的标签
	 *
	 * @param int $uid
	 * @param string $tag
	 * @param int $time
	 * @return array
	 */
	public function addUserTagToUid($uid, $tag, $time) {
		if ($uid < 1) return new PwError('USER:tag.uid.require');
		if (!($tag = trim($tag))) return new PwError('USER:tag.name.require');
		if (($r = $this->allowAdd($uid)) instanceof PwError) return $r;
		
		$info = $this->_getTagDs()->getTagByName($tag);
		$tagDm = new PwUserTagDm();
		$tag_id = 0;
		if ($info) {
			$ifExist = $this->_getTagRelationDs()->getRelationByUidAndTagid($uid, $info['tag_id']);
			if ($ifExist) return new PwError('USER:tag.relation.exists');
			$tagDm->setTagid($info['tag_id'])
				->increaseCount(1);
			$tag_id = $info['tag_id'];
			if (($r = $this->_getTagDs()->updateTag($tagDm)) instanceof PwError) {
				return $r;
			}
		} else {
			$tagDm->setName($tag)->setIfhot(1)->setUsedcount(1);
			if (($tag_id = $this->_getTagDs()->addTag($tagDm)) instanceof PwError) {
				return $tag_id;
			}
		}
		$tag_id && $r = $this->_getTagRelationDs()->addRelation($uid, $tag_id, $time ? intval($time) : Pw::getTime());
		return $r instanceof PwError ? $r : $tag_id;
	}
	
	/**
	 * 将用户和用户标签添加关联
	 *
	 * @param int $uid
	 * @param int $tag_id
	 * @param int $time
	 * @return array
	 */
	public function addTagRelationWithTagid($uid, $tag_id, $time) {
		if ($uid < 1) return new PwError('USER:tag.uid.require');
		if (1 > ($tag_id = intval($tag_id))) return new PwError('USER:tag.id.require');
		if (($r = $this->allowAdd($uid)) instanceof PwError) return $r;
		
		$info = $this->_getTagDs()->getTag($tag_id);
		if (!$info) return new PwError('USER:tag.id.require');
		$r = $this->_getTagRelationDs()->getRelationByUidAndTagid($uid, $info['tag_id']);
		if ($r) return new PwError('USER:tag.relation.exists');
		$tagDm = new PwUserTagDm();
		$tagDm->setTagid($tag_id)
			->increaseCount(1);
		if (($r = $this->_getTagDs()->updateTag($tagDm)) instanceof PwError) {
			return $r;
		}
		return $this->_getTagRelationDs()->addRelation($uid, $tag_id, $time ? intval($time) : Pw::getTime());
	}
	
	/**
	 * 是否允许继续添加
	 *
	 * @param int $uid
	 * @return boolean
	 */
	public function allowAdd($uid) {
		$num = $this->_getTagRelationDs()->countByUid($uid);
		if ($num == 10) return new PwError('USER:tag.owntag.limit.over', array('{num}' => 10));
		return 10 - $num;
	}
	
	/**
	 * 获得标签DS
	 *
	 * @return PwUserTag
	 */
	private function _getTagDs() {
		return Wekit::load('usertag.PwUserTag');
	}
	
	/**
	 * 获得标签关系DS
	 *
	 * @return PwUserTagRelation
	 */
	private function _getTagRelationDs() {
		return Wekit::load('usertag.PwUserTagRelation');
	}
}