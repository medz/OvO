<?php

/**
 * 用户状态的hook
 * 
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id$
 * @package src.hooks.PwUser
 */
class PwUserDoRegisterCheck {
	/**
	 * 根据用户ID删除用户的状态数据记录
	 * 
	 * @param int $uid 用户ID
	 * @return boolean|PwError
	 */
	public function deleteUser($uid) {
		return $this->_getRegisterCheckDs()->deleteUser($uid);
	}
	
	/**
	 * 根据用户ID列表批量删除用户数据
	 *
	 * @param array $uids
	 * @return boolean|PwError
	 */
	public function batchDeleteUser($uids) {
		return $this->_getRegisterCheckDs()->batchDeleteUser($uids);
	}
	
	/**
	 * 获得状态DS
	 *
	 * @return PwUserRegisterCheck
	 */
	private function _getRegisterCheckDs() {
		return Wekit::load('user.PwUserRegisterCheck');
	}
}