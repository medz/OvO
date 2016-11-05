<?php

/**
 * 用户退出之前的钩子接口
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogoutDoBase.php 15902 2012-08-15 07:41:10Z xiaoxia.xuxx $
 * @package src.service.user.srv.logout
 */
abstract class PwLogoutDoBase {
	/**
	 * 用户退出之前的更新
	 *
	 * @param PwUserBo $bo
	 */
	abstract public function beforeLogout(PwUserBo $bo);
}