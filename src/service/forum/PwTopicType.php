<?php
defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * @author peihong <jhqblxt@gmail.com> Nov 23, 2011
 * @link
 * @copyright
 * @license
 */

class PwTopicType {
		
	/**
	 * 增加主题分类
	 *
	 * @param PwTopicTypeDm $dm 版块数据模型
	 * return mixed
	 */
	public function addTopicType($dm) {
		if (($result = $dm->beforeAdd()) !== true) {
			return $result;
		}
		$fields = $dm->getData();
		return $this->_getDao()->addTopicType($fields);
	}
	
	/**
	 * 增加主题分类
	 *
	 * @param PwTopicTypeDm $dm 版块数据模型
	 * return mixed
	 */
	public function updateTopicType($dm) {
		if (($result = $dm->beforeUpdate()) !== true) {
			return $result;
		}
		$fields = $dm->getData();
		return $this->_getDao()->updateTopicType($dm->getId(),$fields);
	}
	
	public function getTopicType($id) {
		$id = intval($id);
		if ($id < 1) return array();
		return $this->_getDao()->getTopicType($id);
	}
	
	public function fetchTopicType($ids) {
		if (!is_array($ids) || !count($ids)) {
			return array();
		}
		return $this->_getDao()->fetchTopicType($ids);
	}
	
	public function getTypesByFid($fid) {
		$fid = intval($fid);
		if ($fid < 1) return array(); 
		return $this->_getDao()->getTopicTypesByFid($fid);
	}
	
	/**
	 * 根据FID获取主题分类数据
	 * 
	 * @param int $fid 版块ID
	 * @param bool $filterAdmin true-过滤掉管理专用的项
	 */
	public function getTopicTypesByFid($fid, $filterAdmin = false) {
		$topicTypes = array('topic_types' => array(),'sub_topic_types' => array() , 'all_types' => array());
		$fid = intval($fid);
		$data = $this->_getDao()->getTopicTypesByFid($fid);
		if (!$data) return $topicTypes;
		foreach ($data as $v) {
			$k = $v['id'];
			if ($v['parentid'] > 0){
				$topicTypes['sub_topic_types'][$v['parentid']][$k] = $v;
			} else {
				$topicTypes['topic_types'][$k] = $v;
			}
			$topicTypes['all_types'][$k] = $v;
		}
		//过滤管理专用
		if ($filterAdmin) {
			foreach ($topicTypes['all_types'] as $k => $v) {
				if ($v['issys']) {
					if ($v['parentid'] > 0) {
						if (isset($topicTypes['sub_topic_types'][$v['parentid']])) unset($topicTypes['sub_topic_types'][$v['parentid']][$k]);
					} else {
						unset($topicTypes['topic_types'][$k]);
						if (isset($topicTypes['sub_topic_types'][$k])) unset($topicTypes['sub_topic_types'][$k]);
					}
					unset($topicTypes['all_types'][$k]);
				}
			}
		}
		return $topicTypes;
	}
	
	/**
	 * 删除一个主题分类
	 * 
	 * @param int $id
	 */
	public function deleteTopicType($id) {
		$topicType = $this->getTopicType($id);
		if ($topicType['parentid'] == 0) {
			$this->deleteTopicTypesByParentid($id);
		}
		return $this->_getDao()->deleteTopicType($id);
	}
	
	public function deleteTopicTypeByFid($fid){
		return $this->_getDao()->deleteTopicTypeByFid($fid);
	}
	
	/**
	 * 根据parentid删除一组 topic types
	 * 
	 * @param int $parentid
	 */
	public function deleteTopicTypesByParentid($parentid) {
		return $this->_getDao()->deleteTopicTypesByParentid($parentid);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * 
	 * @return PwTopicTypeDao
	 * 
	 */
	protected function _getDao() {
		return Wekit::loadDao('forum.dao.PwTopicTypeDao');
	}
}