<?php
defined('WEKIT_VERSION') or exit(403);

/**
 * 后台默认菜单配置信息,菜单配置格式如下：
 * 一个菜单个配置格式中包含: 菜单名称, 路由信息, 菜单图标, 菜单tip, 父节点, 上一个菜单
 * 菜单:  'key' => array('菜单名称', '应用路由', 'icon' , ' tip' ,'父节点key', '上一个菜单key'),
 *
 * <note>
 * 1. 如果没有填写上一个菜单则默认放置在节点最后.
 * 2. 如果没有父节点则并放置在'上一个菜单之后'.
 * 3. 如果'父节点','上一个菜单'都没有则散落的放置在最外层.
 * </note>
 *
 * 节点定义: 'Key' => array('节点名称', 子菜单, 'icon', 'tip' ,'父节点key'),
 */
return array(
	/*========为了演示，将后台导航菜单添加完善=========*/
//	'offen' => array('常用', array()),
//	'offen' => array('常用', '', '', '', ''),


	/**=====配置开始于此=====**/
	'custom' => array('常用', array()),
	'admin' => array('创始人', array()),

	'custom_set' => array('常用菜单', 'custom/*', '', '', 'custom'),
	'admin_founder' => array('创始人管理', 'founder/*', '', '', 'admin'),
	'admin_auth' => array('后台权限', 'auth,role/*', '', '', 'admin'),
	'admin_safe' => array('后台安全', 'safe/*', '', '', 'admin'),

	//混乱的配置，先统一，后续再系统规划整理
	'_extensions' => array(
		//'config' => array('resource' => 'APPS:config.conf.configmenu.php'),//全局
	),
);
/**=====配置结束于此=====**/
