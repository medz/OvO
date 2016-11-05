<?php

/**
 * 用户标签DAO
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserTagDao.php 17269 2012-09-04 08:04:26Z xiaoxia.xuxx $
 * @package src.service.usertag.dao
 */
class PwUserTagDao extends PwBaseDao {
	protected $_table = 'user_tag';
	protected $_pk = 'tag_id';
	protected $_dataStruct = array('tag_id', 'name', 'ifhot', 'used_count');
	
	/**
	 * 根据标签ID获得该标签信息
	 *
	 * @param int $tag_id
	 * @return array
	 */
	public function getTag($tag_id) {
		return $this->_get($tag_id);
	}
	
	/**
	 * 根据标签ID列表批量获取标签信息
	 *
	 * @param array $tag_ids
	 * @return array
	 */
	public function fetchTag($tag_ids) {
		return $this->_fetch($tag_ids, 'tag_id');
	}
	
	/**
	 * 根据标签名字获取标签
	 *
	 * @param string $name
	 * @return array
	 */
	public function getTagByName($name) {
		$sql = $this->_bindTable('SELECT * FROM %s WHERE `name` = ?');
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getOne(array($name));
	}
	
	/**
	 * 获得热门标签
	 *
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function getHotTag($limit, $start = 0) {
		$sql = $this->_bindSql('SELECT * FROM %s WHERE ifhot = 1 %s', $this->getTable(), $this->sqlLimit($limit, $start));
		return $this->getConnection()->query($sql)->fetchAll('tag_id');
	}
	
	/**
	 * 统计热门标签
	 *
	 * @return int
	 */
	public function countHotTag() {
		$sql = $this->_bindTable('SELECT COUNT(*) FROM %s WHERE ifhot = 1 ');
		return $this->getConnection()->query($sql)->fetchColumn(0);
	}
	
	/**
	 * 添加标签
	 *
	 * @param array $data
	 * @return int
	 */
	public function addTag($data) {
		return $this->_add($data, true);
	}
	
	/**
	 * 批量添加标签
	 *
	 * @param array $data
	 * @return int
	 */
	public function batchAddTag($data) {
		$clear = array();
		foreach ($data as $_item) {
			if (!($_tmp = $this->_filterStruct($_item))) continue;
			$clear[] = array($_item['name'], $_item['ifhot']);
		}
		if (!$clear) return false;
		$sql = $this->_bindSql('INSERT INTO %s (`name`, `ifhot`) VALUES %s', $this->getTable(), $this->sqlMulti($clear));
		return $this->getConnection()->execute($sql);
	}
	
	/**
	 * 修改标签
	 *
	 * @param int $tag_id 标签ID
	 * @param array $data 标签数据
	 * @return int
	 */
	public function updateTag($tag_id, $data, $incrementData) {
		return $this->_update($tag_id, $data, $incrementData);
	}
	
	/**
	 * 批量修改标签
	 *
	 * @param array $tag_ids
	 * @param int $ifhot
	 * @return boolean
	 */
	public function batchUpdateTag($tag_ids, $ifhot) {
		$sql = $this->_bindSql('UPDATE %s SET `ifhot` = ? WHERE `tag_id` IN %s', $this->getTable(), $this->sqlImplode($tag_ids));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->execute(array($ifhot));
	}
	
	/**
	 * 根据标签ID删除标签
	 *
	 * @param int $tag_id
	 * @return int
	 */
	public function deleteTag($tag_id) {
		PwSimpleHook::getInstance('PwUserTagDao_deleteTag')->runDo($tag_id);
		return $this->_delete($tag_id);
	}
	
	/**
	 * 批量删除标签
	 *
	 * @param array $tag_ids
	 * @return boolean
	 */
	public function batchDeleteTag($tag_ids) {
		PwSimpleHook::getInstance('PwUserTagDao_batchDeleteTag')->runDo($tag_ids);
		return $this->_batchDelete($tag_ids);
	}
	
	
	/**
	 * 根据条件搜索标签
	 *
	 * @param array $condition
	 * @param int $limit
	 * @param int $start
	 * @return array
	 */
	public function searchTag($condition, $limit, $start = 0) {
		list($where, $params) = $this->_buildConditions($condition);
		$sql = $this->_bindSql('SELECT * FROM %s %s %s', $this->getTable(), $where, $this->sqlLimit($limit, $start));
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->queryAll($params, 'tag_id');	
	}
	
	/**
	 * 根据条件统计标签
	 *
	 * @param array $condition
	 * @return int
	 */
	public function countSearchTag($condition) {
		list($where, $params) = $this->_buildConditions($condition);
		$sql = $this->_bindSql('SELECT COUNT(*) FROM %s %s', $this->getTable(), $where);
		$smt = $this->getConnection()->createStatement($sql);
		return $smt->getValue($params);
	}
	
	/**
	 * 标签搜索
	 *  TODO
	 * @param array $condition
	 * @return array
	 */
	private function _buildConditions($condition) {
		$_where = $_params = array();
		foreach ($condition as $key => $val) {
			if ($val !== 0 && !$val) continue;
			switch($key) {
				case 'name':
					$_where[] = 'name LIKE ?';
					$_params[] = $val . '%';
					break;
				case 'ifhot':
					$_where[] = 'ifhot = ?';
					$_params[] = $val;
					break;
				case 'min_count':
					$_where[] = 'used_count >= ?';
					$_params[] = $val;
					break;
				case 'max_count':
					$_where[] = 'used_count <= ?';
					$_params[] = $val;
					break;
				default:
					break;
			}
		}
		return $_where ? array('WHERE ' . implode(' AND ', $_where), $_params) : array('', array());
	}
}