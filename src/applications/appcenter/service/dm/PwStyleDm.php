<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * 风格模型
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwStyleDm.php 18955 2012-10-09 07:42:19Z long.shi $
 * @package service.style.dm
 */
class PwStyleDm extends PwBaseDm {
	
	/**
	 * 设置应用id
	 *
	 * @param int $styleid
	 * @return PwStyleDm
	 */
	public function setAppid($appid) {
		$this->_data['app_id'] = $appid;
		return $this;
	}
	
	/**
	 * 设置名称
	 *
	 * @param string $name
	 * @return PwStyleDm
	 */
	public function setName($name) {
		$this->_data['name'] = trim($name);
		return $this;
	}
	
	/**
	 * 设置logo
	 *
	 * @param string $logo
	 * @return PwStyleDm
	 */
	public function setLogo($logo) {
		$this->_data['logo'] = trim($logo);
		return $this;
	}
	
	/**
	 * 设置风格类型，site/space
	 *
	 * @param string $type
	 * @return PwStyleDm
	 */
	public function setType($type) {
		$this->_data['style_type'] = trim($type);
		return $this;
	}
	
	/**
	 * 设置风格文件夹名
	 *
	 * @param string $package
	 * @return PwStyleDm
	 */
	public function setAlias($alias) {
		$this->_data['alias'] = trim($alias);
		return $this;
	}
	
	public function setDescription($value) {
		$this->_data['description'] = $value;
	}
	
	/**
	 * 设置是否默认
	 *
	 * @param int $iscurrent
	 * @return PwStyleDm
	 */
	public function setIsCurrent($iscurrent) {
		$this->_data['iscurrent'] = intval($iscurrent);
		return $this;
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
	
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		// TODO Auto-generated method stub
		$this->_data['iscurrent'] = 0;
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		// TODO Auto-generated method stub
		if (!isset($this->_data['app_id']) || $this->_data['app_id'] < 0) return new PwError('STYLE:id.illegal');
		return true;
	}


}

?>