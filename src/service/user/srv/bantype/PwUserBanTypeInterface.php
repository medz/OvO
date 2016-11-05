<?php

/**
 * 禁止类型扩展接口
 *
 * @author xiaoxia.xu <x_824@sina.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwUserBanTypeInterface.php 20650 2012-11-01 09:10:44Z xiaoxia.xuxx $
 * @package src.service.user.bantype
 */
interface PwUserBanTypeInterface {
	/**
	 * 在用户禁止之后操作
	 * 
	 * @param PwUserBanInfoDm $dm 禁止信息
	 * @return mixed
	 */
	public function afterBan(PwUserBanInfoDm $dm);
	
	/**
	 * 删除禁止记录的时候更新用户相关状态
	 *
	 * @param int $uid
	 * @return mixed
	 */
	public function deleteBan($uid);
	
	/**
	 * 获得禁止的范围
	 *
	 * @param int $fid
	 * @return string
	 */
	public function getExtension($fid);
}