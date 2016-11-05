<?php
Wind::import('SRV:usertag.dm.PwUserTagDm');
/**
 * 删除关系更新该标签的数据
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwDeleteRelationDoUpdateTag.php 8027 2012-04-14 06:54:11Z xiaoxia.xuxx $
 * @package src.service.usertag.srv.do
 */
class PwDeleteRelationDoUpdateTag {
	
	/**
	 * 根据用户ID，将该用户所有有关的标签的使用次数都减一
	 *
	 * @param int $uid
	 * @param PwUserTagRelation $bp
	 * @return boolean
	 */
	public function deleteRelationByUid($uid, PwUserTagRelation $bp) {
		$tag_ids = array_keys($bp->getRelationByUid($uid));
		if ($tag_ids) {
			foreach ($tag_ids as $_item) {
				$tagDm = new PwUserTagDm();
				$tagDm->setTagid($_item)->increaseCount(-1);
				$this->_getTagDs()->updateTag($tagDm);
			}
		}
		return true;
	}
	
	/**
	 * 根据标签ID更新这些标签被使用次数
	 *
	 * @param array $tag_ids
	 * @param PwUserTagRelation $bp
	 * @return boolean
	 */
	public function batchDeleteRelation($tag_ids, PwUserTagRelation $bp) {
		foreach ($tag_ids as $_tid) {
			$tagDm = new PwUserTagDm();
			$tagDm->setTagid($_tid)->increaseCount(-1);
			$this->_getTagDs()->updateTag($tagDm);
		}
		return true;
	}
	
	/**
	 * 根据用户ID列表批量删除用户和标签更新，更新该标签的数字
	 *
	 * @param array $uids
	 * @param PwUserTagRelation $bp
	 * @return boolean
	 */
	public function batchDeleteRelationByUids($uids, PwUserTagRelation $bp) {
		foreach ($uids as $_uid) {
			$this->deleteRelationByUid($_uid, $bp);
		}
		return true;
	}
	
	/**
	 * 标签Ds
	 *
	 * @return PwUserTag
	 */
	private function _getTagDs() {
		return Wekit::load('usertag.PwUserTag');
	}
}