<?php

/**
 * 用户登录扩展接口
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserLoginDoBase.php 15637 2012-08-09 09:20:55Z xiaoxia.xuxx $
 * @package src.service.user.srv.login
 */
abstract class PwUserLoginDoBase {
	
	/**
	 * 登录成功执行
	 * 
	 * @param PwUserBo $userBo 用户的BO对象
	 * @param string $ip 登录的IP地址
	 * @return boolean
	 */
	public function welcome(PwUserBo $userBo, $ip) {
		
	}
	
	/**
	 * 处理用户登录验证通过之后的操作
	 *
	 * @param array $info
	 * @return 
	 */
	public function afterLogin($info) {
		
	}
}