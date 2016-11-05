<?php
/**
 * 空间域名
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwSpaceDomainDao.php 21071 2012-11-27 06:34:14Z long.shi $
 * @package wind
 */
class PwSpaceDomainDao extends PwBaseDao {
	protected $_table = 'space_domain';
	protected $_pk = 'domain';
	protected $_dataStruct = array('uid', 'domain');
	
	/**
	 * 添加空间域名 
	 *
	 * @param unknown_type $uid
	 * @param unknown_type $domain
	 * @return boolean|Ambigous <boolean, number, string, rowCount>
	 */
	public function addDomain($uid, $domain) {
		if (!$domain || !$uid) return false;
		return $this->_add(array('uid' => $uid, 'domain' => $domain), false);
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
		$sql = $this->_bindTable('UPDATE %s SET `domain` = ? WHERE `uid` = ?');
		return $this->getConnection()->createStatement($sql)->update(array($domain, $uid));
	}
	
	/**
	 * 删除空间域名
	 *
	 * @param unknown_type $uid
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function delDomain($uid) {
		return $this->_delete($uid);
	}
	
	/**
	 * 根据域名查询uid
	 *
	 * @param unknown_type $domain
	 * @return string
	 */
	public function getUidByDomain($domain) {
		$sql = $this->_bindTable('SELECT `uid` FROM %s WHERE `domain` = ?');
		return $this->getConnection()->createStatement($sql)->getValue(array($domain));
	}
	

	/**
	 * 根据uid获取域名
	 *
	 * @param unknown_type $uid
	 * @return string
	 */
	public function getDomainByUid($uid) {
		$sql = $this->_bindTable('SELECT `domain` FROM %s WHERE `uid` = ?');
		return $this->getConnection()->createStatement($sql)->getValue(array($uid));
	}
	
}

?>