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
	'config' => array('全局', array()),
	'u' => array('用户', array()),
	'contents' => array('内容', array()),
	//'bbs' => array('论坛', array()),
	//'design' => array('门户', array()),
	//'mobile' => array('手机', array()),
	'appcenter' => array('应用', array()),
	//'platform' => array('云平台', array()),

	'custom_set' => array('常用菜单', 'custom/*', '', '', 'custom'),
	'admin_founder' => array('创始人管理', 'founder/*', '', '', 'admin'),
	'admin_auth' => array('后台权限', 'auth,role/*', '', '', 'admin'),
	'admin_safe' => array('后台安全', 'safe/*', '', '', 'admin'),
	
	//'windid_windid' => array('WindID设置', 'windid/windid/*', '', '', 'admin'),
	'windid_client' => array('客户端管理', 'windid/client/*', '', '', 'admin'),
	'windid_notify' => array('通知队列', 'windid/notify/*', '', '', 'admin'),

	'windid_site' => array('站点设置', 'windid/site/*', '', '', 'config'),
	'windid_regist' => array('注册设置', 'windid/regist/*', '', '', 'config'),
	'windid_storage' => array('头像存储设置', 'windid/storage/*', '', '', 'config'),
	'windid_credit' => array('积分设置', 'windid/credit/*', '', '', 'config'),
	'windid_area' => array('地区库', 'windid/areadata/*', '', '', 'config'),
	'windid_school' => array('学校库', 'windid/schooldata/*', '', '', 'config'),

	'windid_user' => array('用户管理', 'windid/user/*', '', '', 'u'),
	'windid_messages' => array('私信管理', 'windid/messages/*', '', '', 'contents'),
	
	'platform_index'   => array('应用管理', 'appcenter,app/app,develop,manage/*', '', '', 'appcenter'),

	//混乱的配置，先统一，后续再系统规划整理
	'_extensions' => array(
		//'config' => array('resource' => 'APPS:config.conf.configmenu.php'),//全局
		//'nav' => array('resource' => 'APPS:nav.conf.navmenu.php'),
		//'credit' => array('resource' => 'APPS:credit.conf.creditmenu.php'),
		//'seo' => array('resource' => 'APPS:seo.conf.seomenu.php'),
		//'rewrite' => array('resource' => 'APPS:rewrite.conf.rewritemenu.php'),
		//'u' => array('resource' => 'APPS:u.conf.umenu.php'),//用户
		//'tag'	=> array('resource' => 'APPS:tag.conf.tagmenu.php'),//话题
		//'message' => array('resource' => 'APPS:message.conf.messagemenu.php'),//消息
		//'report' => array('resource' => 'APPS:report.conf.reportmenu.php'),//举报
		//'bbs' => array('resource' => 'APPS:bbs.conf.bbsmenu.php'),//论坛
		//'other' => array('resource' => 'ADMIN:conf.testmenu.php'),//临时的门户、手机、数据
	
		//'backup' => array('resource' => 'APPS:backup.conf.backupmenu.php'),//临时的门户、手机、数据

		//'word' => array('resource' => 'APPS:word.conf.wordmenu.php'),

		//'link' => array('resource' => 'APPS:link.conf.linkmenu.php'),//运营
		//'punch' => array('resource' => 'APPS:u.conf.punchmenu.php'),
		//'appcenter' => array('resource' => 'APPCENTER:conf.appcentermenu.php'),//应用
		//'medal'	=> array('resource' => 'APPS:medal.conf.medalmenu.php'),
		//'task'	=> array('resource' => 'APPS:task.conf.taskmenu.php'),
		//'vote'	=> array('resource' => 'APPS:vote.conf.votemenu.php'),
		//'announce'	=> array('resource' => 'APPS:announce.conf.announcemenu.php'),
		//'emotion' => array('resource' => 'APPS:emotion.conf.emotionmenu.php'),
		//'cron' => array('resource' => 'APPS:cron.conf.cronmenu.php'),
	),
);
/**=====配置结束于此=====**/
