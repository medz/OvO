<?php
/**
 * Enter description here ...
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSpaceDomain.php 21071 2012-11-27 06:34:14Z long.shi $
 * @package wind
 */
class PwSpaceDomain {
	
	/**
	 * 添加空间域名 
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $domain
	 * @return boolean|Ambigous <boolean, number, string, rowCount>
	 */
	public function addDomain($uid, $domain) {
		if (!$domain || !$uid) return false;
		return $this->_ds()->addDomain($uid, $domain);
	}
	
	/**
	 * 更新空间域名
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $domain
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function updateDomain($uid, $domain) {
		if (!$domain || !$uid) return false;
		return $this->_ds()->updateDomain($uid, $domain);
	}
	
	/**
	 * 删除空间域名
	 *
	 * @param unknown_type $uid
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function delDomain($uid) {
		return $this->_ds()->delDomain($uid);
	}
	
	/**
	 * 根据域名查询uid
	 *
	 * @param unknown_type $domain
	 * @return string
	 */
	public function getUidByDomain($domain) {
		if (!$domain) return 0;
		return $this->_ds()->getUidByDomain($domain);
	}
	
	/**
	 * 根据uid获取域名
	 *
	 * @param unknown_type $uid
	 * @return string
	 */
	public function getDomainByUid($uid) {
		if (!$uid) return '';
		return $this->_ds()->getDomainByUid($uid);
	}
	
	/**
	 * @return PwSpaceDomainDao
	 */
	private function _ds() {
		return Wekit::loadDao('domain.dao.PwSpaceDomainDao');
	}
}

?>