<?php

/**
 * 用户激活码服务
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwUserActiveCode.php 7291 2012-04-01 03:39:14Z xiaoxia.xuxx $
 * @package src.service.user
 */
class PwUserActiveCode {

	const REGIST = 1;//注册类型
	const RESETPWD = 2;//找回密码
	
	/** 
	 * 根据用户ID获得信息
	 *
	 * @param int $uid 用户ID
	 * @param int $typeid 类型
	 * @return array
	 */
	public function getInfoByUid($uid, $typeid = self::REGIST) {
		if (($uid = intval($uid)) <= 0) return array();
		return $this->getDao()->getInfoByUid($uid, $typeid);
	}
	
	/** 
	 * 添加激活码
	 *
	 * @param int $uid 用户ID
	 * @param string $email 发送激活码的Email
	 * @param string $code 激活码
	 * @param int $time 发送激活码时间
	 * @param int $typeid 激活码类型
	 * @return mixed
	 */
	public function addActiveCode($uid, $email, $code, $time, $typeid = self::REGIST) {
		if (($uid = intval($uid)) <= 0) return new PwError('USER:illegal.id');
		if (!WindValidator::isEmail($email)) return new PwError('USER:user.error.-7');
		$data = array('uid' => $uid, 'email' => $email, 'code' => $code, 'send_time' => $time, 'typeid' => $typeid);
		return $this->getDao()->insert($data);
	}
	
	/** 
	 * 激活帐号
	 *
	 * @param int $uid 用户ID
	 * @param int $activeTime 激活时间
	 * @return boolean
	 */
	public function activeCode($uid, $activeTime) {
		if (($uid = intval($uid)) <= 0) return new PwError('USER:illegal.id');
		return $this->getDao()->update($uid, $activeTime);
	}
	
	/** 
	 * 根据用户ID删除信息
	 *
	 * @param int $uid
	 * @return mixed
	 */
	public function deleteInfoByUid($uid) {
		if (($uid = intval($uid)) <= 0) return false;
		return $this->getDao()->deleteByUid($uid);
	}
	
	/** 
	 * 返回Dao对象
	 *
	 * @return PwUserActiveCodeDao
	 */
	private function getDao() {
		return Wekit::loadDao('user.dao.PwUserActiveCodeDao');
	}
}