<?php
/**
 * Enter description here ...
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUpgradeLog.php 21577 2012-12-11 08:32:31Z long.shi $
 * @package wind
 */
class PwUpgradeLog {
	
	const UPGRADE = 1;
	const PATCH = 2;
	
	/**
	 * 获取一个日志
	 *
	 * @param unknown_type $id
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		$r = $this->_dao()->get($id);
		$r && $r['data'] = unserialize($r['data']);
		return $r;
	}
	
	/**
	 * 根据类型获取
	 *
	 * @param unknown_type $type
	 * @return Ambigous <multitype:, multitype:multitype: Ambigous <multitype:,
	 *         multitype:unknown , mixed> >
	 */
	public function getByType($type) {
		return $this->_dao()->getByType($type);
	}
	
	/**
	 * 添加一个日誌
	 *
	 * @param unknown_type $id
	 * @param unknown_type $rule
	 * @param unknown_type $name
	 * @param unknown_type $status
	 * @param unknown_type $time
	 * @return Ambigous <boolean, number, string, rowCount>
	 */
	public function addLog($id, $data, $type = self::UPGRADE) {
		return $this->_dao()->add($id, $type, $data);
	}
	
	/**
	 * 删除一个补丁
	 *
	 * @param unknown_type $id
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function delete($id) {
		return $this->_delete($id);
	}
	
	/**
	 * @return PwUpgradeLogDao
	 */
	private function _dao() {
		return Wekit::loadDao('patch.dao.PwUpgradeLogDao');
	}
}

?>