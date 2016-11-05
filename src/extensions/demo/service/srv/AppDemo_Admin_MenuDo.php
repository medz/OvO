<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 后台菜单扩展
 *
 * @author pw <pw@aliyun-inc.com>
 * @copyright http://www.phpwind.net/u-htm-uid-1793211.html
 * @license http://www.phpwind.net/u-htm-uid-1793211.html
 */
class AppDemo_Admin_MenuDo {
	
	/**
	 * @param array $config 后台菜单配置
	 * @return array
	 */
	public function appDemoDo($config) {
		$config += array(
			'app_demo' => array('demo管理', 'app/manage/*?app=demo', '', '', 'appcenter'),
			);
		return $config;
	}
}

?>