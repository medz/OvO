<?php
/**
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwApplicationDm.php 20013 2012-10-22 10:25:47Z jieyin $
 * @package products
 * @subpackage appcenter.service.dm
 */
class PwApplicationDm extends PwBaseDm {
	protected $_data_log = array();

	public function setLogo($value) {
		$this->_data['logo'] = $value;
	}

	public function setDescription($value) {
		$this->_data['description'] = $value;
	}

	public function setModifiedTime($value) {
		$this->_data['modified_time'] = $value;
	}

	public function setCreatedTime($value) {
		$this->_data['created_time'] = $value;
	}

	public function setPwVersion($value) {
		$this->_data['pwversion'] = $value;
	}

	public function setReleaseData($value) {
		$this->_data['release_data'] = $value;
	}
	
	public function setStatus($value) {
		$this->_data['status'] = $value;
	}

	public function setVersion($value) {
		$this->_data['version'] = $value;
	}

	public function setWebsite($value) {
		$this->_data['website'] = $value;
	}

	public function setAuthorEmail($value) {
		$this->_data['author_email'] = $value;
	}

	public function setAuthorIcon($value) {
		$this->_data['author_icon'] = $value;
	}

	public function setAuthorName($value) {
		$this->_data['author_name'] = $value;
	}

	public function setName($value) {
		$this->_data['name'] = $value;
	}

	public function setAlias($value) {
		$this->_data['alias'] = $value;
	}

	public function setAppId($value) {
		$this->_data['app_id'] = $value;
	}

	public function setAppLog($log, $type = self::LOG_TYPE_DB) {
		$type = $type === self::LOG_TYPE_FILE ? $type : self::LOG_TYPE_DB;
		$this->_data_log[] = array(
			'app_id' => $this->_data['app_id'], 
			'created_time' => Pw::getTime(), 
			'modified_time' => Pw::getTime(), 
			'data' => $log, 
			'log_type' => $type);
	}

	/**
	 * @see PwBaseDm::getData()
	 * @return multitype:
	 */
	public function getLogData() {
		return $this->_data_log;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		return !empty($this->_data['name']) && !empty($this->_data['alias']) && !empty($this->_data['version']) && !empty(
			$this->_data['pwversion']);
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		return true;
	}
}

?>