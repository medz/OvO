<?php

/**
 * 友情链接
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLinkService.php 28814 2013-05-24 09:31:14Z jieyin $
 * @package src.service.link.srv
 */
class PwLinkService {
	
	/**
	 * 根据链接ID列表,批量删除友情链接
	 *
	 * @param array $lids
	 * @return boolean
	 */
	public function batchDelete($lids) {
		$lids = (!is_array($lids) && $lids) ? array($lids) : $lids;
		$this->_getDs()->batchDelRelationsByLid($lids);
		return $this->_getDs()->batchDelete($lids);
	}

	/**
	 * 根据分类组装数据
	 *
	 * @param int $typeid
	 * @return int
	 */
	public function getTypesByTypeId($typeid = null) {
		foreach ($this->_getDs()->getByTypeId($typeid) as $value) {
			$lids[] = $value['lid'];
			$types[$value['lid']][] = $value['typeid'];
		}
		return array($lids, $types);
	}

	/**
	 * 获取链接列表
	 *
	 * @param int $typeId
	 * @return array
	 */
	public function getLinksList($typeId = 0) {
		$links = $typeIds = $linksList = array();
		if (!$typeId) {
			$links = $this->_getDs()->getLinks(0, 500);
			$_typeIds = $this->_getDs()->fetchRelationsByLinkid(array_keys($links));
			foreach ($_typeIds as $_relation) {
				$typeIds[$_relation['lid']][] = $_relation['typeid'];
			}
		} else {
			list($lids, $typeIds) = $this->getTypesByTypeId($typeId);
			$links = $this->_getDs()->getLinksByLids($lids);
		}
		
		if (!$links) return array();
		foreach ($links as $key => $value) {
			$value['typeid'] = (array)$typeIds[$value['lid']];
			$linksList[$key] = $value;
		}
		
		return $linksList;
	}

	/**
	 * 获取未审核链接列表
	 *
	 * @param int $ifcheck 0 未审核| 1已审核
	 * @param int $start
	 * @param int $limit
	 * @return array
	 */
	public function getCheckLinksList($start, $limit, $ifcheck) {
		$count = $this->_getDs()->countLinks($ifcheck);
		if (!$count) return array(0, array());
		$links = $this->_getDs()->getLinks($start, $limit, $ifcheck);
		if ($links) {
			$relations = $this->_getDs()->fetchRelationsByLinkid(array_keys($links));
			foreach ($relations as $_item) {
				if (!isset($links[$_item['lid']]['typeids'])) {
					$links[$_item['lid']]['typeids'] = array();
				}
				$links[$_item['lid']]['typeids'][] = $_item['typeid'];
			}
		}
		return array($count, $links);
	}

	/**
	 * 根据分类标识组装logo和非logo数据
	 *
	 * @param string $typename
	 * @return array
	 */
	public function getLinksByType($typename) {
		$type = $this->_getDs()->getTypeByName($typename);
		if (!$type) {
			return array();
		}
		list($lids) = $this->getTypesByTypeId($type['typeid']);
		$linkList = array();
		foreach ($this->_getDs()->getLinksByLids($lids) as $link) {
			if ($link['logo']) {
				$linkList['logo'][] = $link;
				continue;
			}
			$linkList['text'][] = $link;
		}
		return $linkList;
	}

	/**
	 * 分类列表
	 *
	 * @return array
	 */
	public function getAllLinkTypes() {
		$linkCount = $this->_getDs()->countLinkTypes();
		$linkTypes = array();
		foreach ($this->_getDs()->getAllTypes() as $k => $v) {
			$v['linknum'] = isset($linkCount[$k]['linknum']) ? $linkCount[$k]['linknum'] : 0;
			$linkTypes[$v['typeid']] = $v;
		}
		return $linkTypes;
	}

	/**
	 * 获得link的DS
	 *
	 * @return PwLink
	 */
	private function _getDs() {
		return Wekit::load('link.PwLink');
	}
}
