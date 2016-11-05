<?php

/**
 * 邀请码DS
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwInviteCode.php 19073 2012-10-10 08:33:40Z xiaoxia.xuxx $
 * @package service.invite
 */
class PwInviteCode {

	/**
	 * 根据邀请码获得邀请码信息
	 *
	 * @param string $code
	 * @return array
	 */
	public function getCode($code) {
		if (!$code) return array();
		return $this->_getDao()->getCode($code);
	}
	
	/**
	 * 根据创建者ID获得已使用的邀请码列表
	 *
	 * @param int $createdUid
	 * @return array
	 */
	public function getUsedCodeByCreatedUid($createdUid, $limit = 18, $start = 0) {
		if (!$createdUid) return array();
		return $this->_getDao()->getUsedCodeByCreatedUid($createdUid, $limit, $start);
	}
	
	/**
	 * 根据用户ID统计该用户邀请的人数
	 *
	 * @param int $createdUid
	 * @return array
	 */
	public function countUsedCodeByCreatedUid($createdUid) {
		if (!$createdUid) return 0;
		return $this->_getDao()->countUsedCodeByCreatedUid($createdUid);
	}
	
	/**
	 * 搜索邀请码
	 *
	 * @param PwInviteCodeSo $condition 搜索条件对象
	 * @param int $limit 搜索的条数
	 * @param int $offset 搜索的开始位置
	 * @return array
	 */
	public function searchCode(PwInviteCodeSo $condition, $limit = 10, $offset = 0) {
		return $this->_getDao()->searchCode($condition->getData(), $limit, $offset);
	}
	
	/**
	 * 根据条件统计邀请码总数
	 *
	 * @param PwInviteCodeSo $condition
	 * @return int
	 */
	public function countSearchCode(PwInviteCodeSo $condition) {
		return $this->_getDao()->countSearchCode($condition->getData());
	}
	
	/**
	 * 根据用户ID和时间，查询该用户在该时间点之后已经购买的
	 *
	 * @param int $uid 用户ID
	 * @param int $time 计算的其实时间
	 * @return int
	 */
	public function countByUidAndTime($uid, $time) {
		return $this->_getDao()->countByUidAndTime($uid, $time);
	}
	
	/**
	 * 添加邀请码
	 *
	 * @param PwInviteCodeDm $dm 邀请码
	 * @return boolean
	 */
	public function addCode(PwInviteCodeDm $dm) {
		if (($r = $dm->beforeAdd()) instanceof PwError) return $r;
		return $this->_getDao()->addCode($dm->getData());
	}
	
	/**
	 * 批量添加邀请码
	 *
	 * @param array $codeDms 邀请码信息
	 * @return boolean
	 */
	public function batchAddCode($codeDms) {
		if (empty($codeDms) || !is_array($codeDms)) return false;
		$data = array();
		foreach ($codeDms as $dm) {
			if (!($dm instanceof PwInviteCodeDm)) return new PwError('USER:invite.data.format.error');
			if (($r = $dm->beforeAdd()) instanceof PwError) return $r;
			$data[] = $dm->getData();
		}
		return $this->_getDao()->batchAddCode($data);
	}
	
	/**
	 * 更新邀请码信息
	 *
	 * @param PwInviteCodeDm $dm 邀请码的购买者---当注册用户购买邀请码注册，成功之后更新该邀请码的购买者
	 * @return boolean
	 */
	public function updateCode(PwInviteCodeDm $dm) {
		if (($r = $dm->beforeUpdate()) instanceof PwError) return $r;
		return $this->_getDao()->updateCode($dm->code, $dm->getData());
	}
	
	/**
	 * 删除邀请码
	 *
	 * @param string $code
	 * @return boolean
	 */
	public function deleteCode($code) {
		return $this->_getDao()->deleteCode($code);
	}
	
	/**
	 * 批量删除邀请码
	 *
	 * @param array $cods
	 * @return boolean
	 */
	public function batchDeleteCode($codes) {
		return $this->_getDao()->batchDeleteCode($codes);
	}
	
	/**
	 * 批量过滤已经存在的邀请码
	 *
	 * @param array $codes
	 * @return array
	 */
	public function fetchCode($codes) {
		return $this->_getDao()->fetchCode($codes);
	}
	
	/**
	 * 邀请码DAO
	 *
	 * @return PwInviteCodeDao
	 */
	private function _getDao() {
		return Wekit::loadDao('invite.dao.PwInviteCodeDao');
	}
}