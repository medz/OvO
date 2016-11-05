<?php
Wind::import('SRV:user.srv.logout.PwLogoutDoBase');

/**
 * 用户退出  清除在线状态
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 * @version $Id: PwLogoutDoUpdateOnline.php 17060 2012-08-31 01:50:31Z gao.wanggao $
 * @package src.service.user.srv.logout.do
 */
class PwLogoutDoUpdateOnline extends PwLogoutDoBase {
	
	/* (non-PHPdoc)
	 * @see PwLogoutDoBase::beforeLogout()
	 */
	public function beforeLogout(PwUserBo $bo) {
		if (!$bo->isExists()) return true;
		$srv = Wekit::load('online.srv.PwOnlineService');
		$srv->logoutOnline($bo->uid);
		return true;
	}
}