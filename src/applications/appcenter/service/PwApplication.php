<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwApplication.php 24585 2013-02-01 04:02:37Z jieyin $
 * @package products
 * @subpackage appcenter.service
 */
class PwApplication {

	/**
	 * 添加应用
	 *
	 * @param PwApplicationDm $application        	
	 * @return PwError true
	 */
	public function add($application) {
		$error = $application->beforeAdd();
		if (true !== $error) return new PwError('APPCENTER:validate.fail', array('error' => $error));
		return $this->_load()->add($application->getData());
	}

	/**
	 * 更具应用ID更新应用
	 *
	 * @param PwApplicationDm $application        	
	 * @return PwError true
	 */
	public function update($application) {
		if (true !== ($error = $application->beforeUpdate())) return new PwError('APPCENTER:validate.fail', 
			array('error' => $error));
		$_r = $this->_load()->update($application->getField('app_id'), $application->getData());
		if (!$_r) return new PwError('APPCENTER:update.fail');
		return true;
	}

	/**
	 * 根据appid删除
	 *
	 * @param int $app_id
	 * @return PwError|boolean
	 */
	public function delByAppId($app_id) {
		return $this->_load()->delByAppId($app_id);
	}

	/**
	 * 根据应用ID查找应用
	 *
	 * @param string $appId        	
	 * @return PwError boolean
	 */
	public function findByAppId($appId) {
		return $this->_load()->findByAppId($appId);
	}
	
	/**
	 * 根据应用名称模糊搜索
	 *
	 * @param string $name
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function searchByName($name, $num = 10, $start = 0) {
		return $this->_load()->searchByName($name, $num, $start);
	}
	
	/**
	 * 统计搜索结果
	 *
	 * @param string $name
	 * @return int
	 */
	public function countSearchByName($name) {
		return $this->_load()->countSearchByName($name);
	}

	/**
	 * 根据应用别名查找应用
	 *
	 * @param string $alias
	 * @return PwError|Ambigous <multitype:, boolean, multitype:unknown , mixed>
	 */
	public function findByAlias($alias) {
		return $this->_load()->findByAlias($alias);
	}
	
	/**
	 * 根据应用别名查找应用注册信息，返回app数据
	 *
	 * @param array $alias
	 * @return array
	 */
	public function fetchByAlias($alias, $index = 'app_id') {
		return $this->_load()->fetchByAlias($alias, $index);
	}

	/**
	 * 根据app_id批量获取
	 *
	 * @param array $ids
	 * @return array
	 */
	public function fetchByAppId($ids, $index = 'app_id') {
		return $this->_load()->fetchByAppId($ids, $index);
	}
	
	/**
	 * 根据status获取列表
	 *
	 * @param int $num
	 * @param int $start
	 * @param int $status 是否有独立页面
	 * @param string $orderby
	 * @return array
	 */
	public function fetchListByStatus($num = 10, $start = 0, $status = 1, $orderby = 'created_time') {
		return $this->_load()->fetchListByStatus($num, $start, $status, $orderby);
	}
	
	/**
	 * 根据status获取总数
	 *
	 * @param int $status
	 * @return int
	 */
	public function countByStatus($status = 1) {
		return $this->_load()->countByStatus($status);
	}

	/**
	 * app列表
	 *
	 * @param int $num
	 * @param int $start
	 * @return array
	 */
	public function fetchByPage($num = 10, $start = 0, $index = 'app_id') {
		return $this->_load()->fetchByPage((int) $num, (int) $start, $index);
	}

	/**
	 * 获取app总数
	 *
	 * @return int
	 */
	public function count() {
		return $this->_load()->count();
	}

	/**
	 *
	 * @return PwApplicationDao
	 */
	private function _load() {
		return Wekit::loadDao('APPCENTER:service.dao.PwApplicationDao');
	}
}

?>