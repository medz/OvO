<?php
/**
 * 后台系统外部服务依赖接口定义
 * 
 * 后台应用需要如果依赖外部的服务，需要在该接口中定义
 * 默认是实现‘ADMIN:service.srv.do’
 * @author Qiong Wu <papa0924@gmail.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: codetemplates(windframework_docs_zend_8.0).xml 2781 2011-09-22 03:59:17Z yishuo $
 * @package wind
 */
interface IAdminUserDependenceService {

	/**
	 * 更新后台用户时（添加，删除）更新用户表对应状态值
	 *
	 * @param boolean $status
	 * @return boolean PwError
	 */
	public function updateUserStatus($uid, $status);

	/**
	 * 验证用用户名，密码并返回用户相信数组
	 *
	 * @param string $username
	 * @param string $passwork
	 * @return PwError array
	 */
	public function verifyUser($username, $passwork);

	/**
	 * 根据用户名获取用户信息
	 *
	 * @param string $username
	 * @return array
	 */
	public function getUserByName($username);

	/**
	 * 用户设置，如果用户存在则修改相关信息，如果用户不存在则添加用户
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $groupid
	 * @param string $uid
	 * @return boolean PwError
	 */
	public function setUser($username, $password, $email, $groupid = 3, $uid = 0);
	
	/**
	 * 获取用户信息列表
	 *
	 * @param array $userids
	 */
	public function getUserByUids($userids);
	
	/**
	 * 根据用户ID获取用户信息
	 *
	 * @param string $userid
	 * @return array['password']
	 */
	public function getUserByUid($userid);
}

?>