<?php
Wind::import('LIB:base.PwBaseDm');
/**
 * domain模型
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $id$
 * @package service.domain.dm
 */
class PwDomainDm extends PwBaseDm {
	
	/**
	 * 设置key
	 *
	 * @param string $key
	 * @return PwDomainDm
	 */
	public function setDomainKey($key) {
		$this->_data['domain_key'] = trim($key);
		return $this;
	}
	
	/**
	 * 设置域名类型
	 *
	 * @param sring $type
	 * @return PwDomainDm
	 */
	public function setDomainType($type) {
		$this->_data['domain_type'] = trim($type);
		return $this;
	}
	
	/**
	 * 设置二级域名
	 *
	 * @param string $domain
	 * @return PwDomainDm
	 */
	public function setDomain($domain) {
		$this->_data['domain'] = trim($domain);
		return $this;
	}
	
	/**
	 * 设置根域名
	 *
	 * @param string $root
	 * @return PwDomainDm
	 */
	public function setRoot($root) {
		$this->_data['root'] = trim($root);
		return $this;
	}
	
	/**
	 * 设置域名首字母
	 *
	 * @param string $root
	 * @return PwDomainDm
	 */
	public function setFirst($first) {
		$this->_data['first'] = trim($first);
		return $this;
	}
	
	/**
	 * 设置域名首字母
	 *
	 * @param string $root
	 * @return PwDomainDm
	 */
	public function setId($id) {
		$this->_data['id'] = intval($id);
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		// TODO Auto-generated method stub
		
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	protected function _beforeUpdate() {
		// TODO Auto-generated method stub
		
	}


}

?>