<?php
/**
 * 风格数据服务层
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyle.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package service.style
 */
class PwStyle {
	
	/**
	 * 添加
	 *
	 * @param PwStyleDm $dm
	 * @return PwError|boolean
	 */
	public function addStyle(PwStyleDm $dm) {
		if (($r = $dm->beforeAdd()) instanceof PwError) return $r;
		return $this->_styleDao()->addStyle($dm->getData());
	}
	
	/**
	 * 修改
	 *
	 * @param int $styleid
	 * @param array $data
	 * @return boolean
	 */
	public function updateStyle(PwStyleDm $dm) {
		if (($r = $dm->beforeUpdate()) instanceof PwError) return $r;
		return $this->_styleDao()->updateStyle($dm->getField('app_id'), $dm->getData());
	}
	
	/**
	 * 删除
	 *
	 * @param int $styleid
	 * @return booelan
	 */
	public function deleteStyle($styleid) {
		return $this->_styleDao()->deleteStyle($styleid);
	}
	
	/**
	 * 统计风格数
	 *
	 * @return int
	 */
	public function countByType($type = 'site') {
		return $this->_styleDao()->countByType(trim($type));
	}
	
	/**
	 * 获取当前风格
	 *
	 * @return array
	 */
	public function getCurrentStyleByType($type = 'site') {
		return $this->_styleDao()->getCurrentStyleByType(trim($type));
	}
	
	/**
	 * 获取所有风格
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param string $orderBy
	 * @return array
	 */
	public function getStyleListByType($type = 'site', $num = 10, $start = 0) {
		return $this->_styleDao()->getStyleListByType(trim($type), (int)$num, (int)$start);
	}
	
	/**
	 * 获取风格详细信息
	 *
	 * @param int $styleid
	 * @return array
	 */
	public function getStyle($styleid) {
		return $this->_styleDao()->getStyle($styleid);
	}
	
	/**
	 * 获取所有风格
	 *
	 * @param string $type
	 */
	public function getAllStyle($type = 'site') {
		return $this->_styleDao()->getAllStyles(trim($type));
	}

	/**
	 * 根据风格目录查找风格
	 *
	 * @param array|string $package
	 */
	public function fetchStyleByAliasAndType($alias, $type = 'site', $index = 'app_id') {
		if (!$alias) return array();
		return $this->_styleDao()->fetchStyleByAliasAndType($alias, $type, $index);
	}
	
	/**
	 * 根据应用ID查找应用
	 *
	 * @param string $appId
	 * @return PwError boolean
	 */
	public function findByAppId($appId) {
		if (!$appId) return new PwError('APPCENTER:validate.fail', array('error' => '应用查找时，应用ID非法'));
		return $this->_styleDao()->findByAppId($appId);
	}
	
	/**
	 * @return PwStyleDao
	 */
	private function _styleDao() {
		return Wekit::loadDao('APPCENTER:service.dao.PwStyleDao');
	}
}

?>