<?php
/**
 * 系统补丁
 *
 * @author Shi Long <long.shi@alibaba-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwPatch.php 21505 2012-12-10 10:21:57Z long.shi $
 * @package wind
 */
class PwPatch {
	
	/**
	 * 获取一个补丁
	 *
	 * @param unknown_type $id
	 * @return Ambigous <Ambigous, multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		$r = $this->_dao()->get($id);
		$r && $r['rule'] = unserialize($r['rule']);
		return $r;
	}
	
	/**
	 * 添加一个补丁
	 *
	 * @param unknown_type $id
	 * @param unknown_type $rule
	 * @param unknown_type $name
	 * @param unknown_type $status
	 * @param unknown_type $time
	 * @return Ambigous <boolean, number, string, rowCount>
	 */
	public function add($id, $rule, $name, $status, $time, $desc) {
		return $this->_dao()->add($id, serialize($rule), $name, $status, $time, $desc);
	}
	
	/**
	 * 更新补丁状态
	 *
	 * @param unknown_type $id
	 * @param unknown_type $status
	 * @return Ambigous <boolean, number, rowCount>
	 */
	public function update($id, $status) {
		return $this->_dao()->update($id, $status);
	}
	
	/**
	 * 更新旧补丁状态
	 *
	 * @param unknown_type $newest
	 * @return Ambigous <rowCount, boolean, number>
	 */
	public function updateOldPatch($newest) {
		return $this->_dao()->updateOldPatch($newest);
	}
	
	/**
	 * 删除一个补丁
	 *
	 * @param unknown_type $id
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function delete($id) {
		return $this->_dao()->delete($id);
	}
	
	/**
	 * 获取补丁列表
	 *
	 */
	public function getList() {
		return $this->_dao()->getList();
	}
	
	/**
	 * 获取最后的补丁
	 *
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function getMaxPatch() {
		return $this->_dao()->getMaxPatch();
	}
	
	/**
	 * @return PwPatchDao
	 */
	private function _dao() {
		return Wekit::loadDao('patch.dao.PwPatchDao');
	}
}

?>