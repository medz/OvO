<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * @author $Author: gao.wanggao $ Foxsee@aliyun.com
 * @copyright ?2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwLikeTag.php 8487 2012-04-19 08:09:57Z gao.wanggao $ 
 * @package 
 */
class PwLikeTag {
	
	/**
	 * 获取一条内容
	 * 
	 * @param int $tagid
	 */
	public function getLikeTag($tagid) {
		$tagid = (int)$tagid;
		if ($tagid < 1) return array();
		return $this->_getLikeTagDao()->getInfo($tagid);
	}
	
	/**
	 * 批量获取内容
	 * 
	 * @param array $tagids
	 */
	public function fetchLikeTag($tagids) {
		if (!is_array($tagids) || count($tagids) < 1) return array();
		return $this->_getLikeTagDao()->getInfoByTags($tagids);
	}
	
	/**
	 * 根据用户ID获取内容
	 * 
	 * @param int $uid
	 */
	public function getInfoByUid($uid) {
		$uid = (int)$uid;
		if ($uid < 1) return array();
		return $this->_getLikeTagDao()->getInfoByUid($uid);
	}
	
	/**
	 * 添加内容
	 * 
	 * @param PwLikeTagDm $dm
	 */
	public function addInfo(PwLikeTagDm $dm) {
		$resource = $dm->beforeAdd();
		if ($resource instanceof PwError) return $resource;
		return $this->_getLikeTagDao()->addInfo($dm->getData());
	}
	
	/**
	 * 修改内容
	 * 
	 * @param int $tagid
	 * @param PwLikeTagDm $dm
	 */
	public function updateInfo(PwLikeTagDm $dm) {
		$resource = $dm->beforeUpdate();
		if ($resource instanceof PwError) return $resource;
		return $this->_getLikeTagDao()->updateInfo($dm->tagid, $dm->getData());
	}
	
	/**
	 * 更新统计
	 * 
	 * @param int $tagid
	 * @param bool $type true +1  false -1
	 */
	public function updateNumber($tagid, $type = true) {
		$tagid = (int)$tagid;
		if ($tagid < 1) return false;
		return $this->_getLikeTagDao()->updateNumber($tagid, $type);
	}
	
	/**
	 * 删除内容
	 * 
	 * @param int $tagid
	 */
	public function deleteInfo($tagid) {
		$tagid = (int)$tagid;
		if ($tagid < 1) return false;
		return $this->_getLikeTagDao()->deleteInfo($tagid);
	}
	
	private function _getLikeTagDao() {
		return Wekit::loadDao('like.dao.PwLikeTagDao');
	}
	
}
?>