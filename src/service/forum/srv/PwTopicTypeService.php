<?php
defined('WEKIT_VERSION') || exit('Forbidden');

class PwTopicTypeService {

	/**
	 * 主题分类特殊显示需求(列表页排序)
	 * 
	 * @param $type
	 * @param array $topictypes
	 * @return array
	 */
	public function sortTopictype($type, $topictypes) {
		$parentid = $topictypes['all_types'][$type]['parentid'];
		$current = $topictypes['all_types'][$type];
		if ($parentid) {
			//选择子分类 插入排序
			$tmp_topic_types = $tmp_sub_topic_types = array();
			//被选中的子分类列表重排序
			$tmp_sub_topic_types[$parentid] = $topictypes['all_types'][$parentid];
			foreach ($topictypes['sub_topic_types'][$parentid] as $k => $v) {
				if ($k == $type) {
					continue;
				}
				$tmp_sub_topic_types[$k] = $v;
			}
			//移除被选中的一级下分类列表
			unset($topictypes['sub_topic_types'][$parentid]);
			$topictypes['sub_topic_types'][$current['id']] = $tmp_sub_topic_types;
			//add sub to top level
			foreach ($topictypes['topic_types'] as $k => $v) {
				if ($k == $parentid) {
					$tmp_topic_types[$current['id']] = $current;
				} else {
					$tmp_topic_types[$k] = $v;
				}
			}
			//新的一级分类排序
			$topictypes['topic_types'] = $tmp_topic_types;
		}
		return $topictypes;
	}
	
	public function getTopictypes($fid) {
		$data = $this->_getTopictypeDs()->getTypesByFid($fid);
		if (!$data) return array();
		$topicTypes = array();
		foreach ($data as $k => $v) {
			if ($v['parentid'] > 0){
				$topicTypes[$v['parentid']]['sub_type'][$k]['name'] = $v['name'];
			} else {
				$tmp['name'] = $v['name'];
				$topicTypes[$k] = $tmp;
			}
		}
		return $topicTypes;
	}
	
	/**
	 * 从A版块复制主题分类至B版块
	 * 
	 * @param int $fromFid
	 * @param int $toFid
	 */
	public function copyTopicType($fromFid,$toFid){
		$this->_getTopictypeDs()->deleteTopicTypeByFid($toFid);
		$topicTypes = $this->_getTopictypeDs()->getTypesByFid($fromFid);
		$idMap = $subTopicTypes = array();
		Wind::import('SRV:forum.dm.PwTopicTypeDm');
		foreach ($topicTypes as $k=>$v) {
			if ($v['parentid']) {
				$subTopicTypes[$k] = $v;
				continue;
			}
			$dm = new PwTopicTypeDm();
			$dm->setFid($toFid)
				->setIsSystem($v['issys'])
				->setVieworder($v['vieworder'])
				->setLogo($v['logo'])
				->setName($v['name']);
			$id = $this->_getTopictypeDs()->addTopicType($dm);
			$idMap[$v['id']] = $id;
		}
		if ($subTopicTypes) {
			foreach ($subTopicTypes as $k=>$v) {
				$dm = new PwTopicTypeDm();
				$dm->setFid($toFid)
					->setIsSystem($v['issys'])
					->setVieworder($v['vieworder'])
					->setLogo($v['logo'])
					->setName($v['name'])
					->setParentId($idMap[$v['parentid']]);
				$this->_getTopictypeDs()->addTopicType($dm);
			}
		}
		return true;
	}
	
	/**
	 * Enter description here ...
	 * 
	 * @return PwTopicType
	 */
	private function _getTopictypeDs(){
		return Wekit::load('forum.PwTopicType');
	}
}