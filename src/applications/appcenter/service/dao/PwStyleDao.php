<?php
Wind::import('LIB:base.PwBaseDao');
/**
 * 风格dao
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyleDao.php 18955 2012-10-09 07:42:19Z long.shi $
 * @package service.style.dao
 */
class PwStyleDao extends PwBaseDao {
	protected $_pk = 'app_id';
	protected $_table = 'style';
	protected $_dataStruct = array('app_id', 'iscurrent', 'style_type', 'name', 'alias', 'logo', 'author_name', 'author_icon', 'author_email', 'website', 'version', 'pwversion', 'created_time', 'modified_time', 'description');

	/**
	 * 添加
	 *
	 * @param array $data        	
	 * @return boolean
	 */
	public function addStyle($data) {
		return $this->_add($data, false);
	}

	/**
	 * 修改
	 *
	 * @param int $styleid        	
	 * @param array $data        	
	 * @return boolean
	 */
	public function updateStyle($styleid, $data) {
		return $this->_update($styleid, $data);
	}

	/**
	 * 删除
	 *
	 * @param int $styleid        	
	 * @return booelan
	 */
	public function deleteStyle($styleid) {
		return $this->_delete($styleid);
	}

	/**
	 * 统计风格数
	 *
	 * @return int
	 */
	public function countByType($type = 'site') {
		$sql = $this->_bindTable("SELECT COUNT(*) FROM %s WHERE `style_type` = ?");
		return $this->getConnection()->createStatement($sql)->getValue(array($type));
	}

	/**
	 * 获取当前风格
	 *
	 * @return array
	 */
	public function getCurrentStyleByType($type = 'site') {
		$sql = $this->_bindTable("SELECT * FROM %s WHERE `iscurrent` = 1 AND `style_type` = ?");
		return $this->getConnection()->createStatement($sql)->getOne(array($type));
	}

	/**
	 * 获取风格列表
	 *
	 * @param int $offset        	
	 * @param int $limit        	
	 * @param string $orderBy        	
	 * @return array
	 */
	public function getStyleListByType($type = 'site', $num = 10, $start = 0) {
		$sql = $this->_bindSql(
			"SELECT * FROM %s WHERE `style_type` = ? ORDER BY `iscurrent` DESC %s", 
			$this->getTable(), $this->sqlLimit($num, $start));
		return $this->getConnection()->createStatement($sql)->queryAll(array($type), $this->_pk);
	}

	/**
	 * 获取所有风格
	 *
	 * @param string $type        	
	 * @return array
	 */
	public function getAllStyles($type = 'site') {
		$sql = $this->_bindTable("SELECT * FROM %s WHERE `style_type` = ?");
		return $this->getConnection()->createStatement($sql)->queryAll(array($type), $this->_pk);
	}

	/**
	 * 获取风格详细信息
	 *
	 * @param int $styleid        	
	 * @return array
	 */
	public function getStyle($styleid) {
		return $this->_get($styleid);
	}

	/**
	 * 根据风格目录查找风格
	 *
	 * @param array|string $package        	
	 */
	public function fetchStyleByAliasAndType($packages, $type = 'site', $index = 'app_id') {
		is_array($packages) || $packages = array($packages);
		$sql = $this->_bindSql("SELECT * FROM %s WHERE `alias` IN %s AND style_type = ?", $this->getTable(), 
			$this->sqlImplode($packages));
		return $this->getConnection()->createStatement($sql)->queryAll(array($type), $index);
	}
	
	/**
	 * 根据ID查找App应用注册信息，返回app数据
	 *
	 * @param string $appId
	 * @return array
	 */
	public function findByAppId($appId) {
		if (!$appId) return false;
		$sql = $this->_bindTable('SELECT * FROM %s ') . ' WHERE app_id=?';
		return $this->getConnection()->createStatement($sql)->getOne(array($appId));
	}
}

?>