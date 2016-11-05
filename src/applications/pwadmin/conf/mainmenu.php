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
	'bbs' => array('论坛', array()),
	'design' => array('门户', array()),
//	'mobile' => array('手机', array()),
	'data' => array('工具', array()),
	'appcenter' => array('应用', array()),
	'platform' => array('插件与模板', array()),

	'custom_set' => array('常用菜单', 'custom/*', '', '', 'custom'),
	'admin_founder' => array('创始人管理', 'founder/*', '', '', 'admin'),
	'admin_auth' => array('后台权限', 'auth,role/*', '', '', 'admin'),
	'admin_safe' => array('后台安全', 'safe/*', '', '', 'admin'),
	
	'windid_windid' => array('WindID设置', 'windidclient/windid/*', '', '', 'admin'),
	'windid_client' => array('客户端管理', 'windidclient/client/*', '', '', 'admin'),
	'windid_notify' => array('通知队列', 'windidclient/notify/*', '', '', 'admin'),

	'config_site' => array('站点设置', 'config/config/*', '', '', 'config'),
	'config_nav' => array('导航设置', 'nav/nav/*', '', '', 'config'),
	'config_register' => array('注册登录', 'config/regist/*', '', '', 'config'),
	//'config_mobile' => array('手机服务', 'config/mobile/*', '', '', 'config'),
	'config_credit' => array('积分设置', 'credit/credit/*', '', '', 'config'),
	'config_editor' => array('编辑器', 'config/editor/*', '', '', 'config'),
	'config_emotion' => array('表情管理', 'emotion/emotion/*', '', '', 'config'),
	'config_attachment' => array('附件相关', 'config/attachment,stroage/*', '', '', 'config'),
	'config_watermark' => array('水印设置', 'config/watermark/*', '', '', 'config'),
	'config_verifycode' => array('验证码', 'verify/verify/*', '', '', 'config'),
	'config_seo' => array('SEO优化', 'seo,app/manage/*', '', '', 'config'),
	'config_rewrite' => array('URL伪静态', 'rewrite/rewrite/*', '', '', 'config'),
	'config_domain' => array('二级域名', 'rewrite/domain/*', '', '', 'config'),
	'config_email' => array('电子邮件', 'config/email/*', '', '', 'config'),
	'config_pay' => array('网上支付', 'config/pay/*', '', '', 'config'),
	'config_area' => array('地区库', 'windidclient/areadata/*', '', '', 'config'),
	'config_school' => array('学校库', 'windidclient/schooldata/*', '', '', 'config'),

	'u_groups' => array('用户组权限', 'u/groups/*', '', '', 'u'),
	'u_upgrade'=> array('用户组提升','u/upgrade/*','','','u'),
	'u_manage' => array('用户管理', 'u/manage/*', '', '', 'u'),
	'u_forbidden' => array('用户禁止', 'u/forbidden/*', '', '', 'u'),
	'u_check' => array('新用户审核', 'u/check/*', '', '', 'u'),

	'bbs_article' => array('帖子管理', 'bbs/article/*', '', '', 'contents'),
	'contents_tag' => array('话题管理', 'tag/manage/*', '', '', 'contents'),
	'contents_message' => array('私信管理', 'message/manage/*', '', '', 'contents'),
	'contents_report' => array('举报管理', 'report/manage/*', '', '', 'contents'),
	//'bbs_contentcheck' => array('内容审核', array(), '', '', 'contents'),
	'bbs_contentcheck_forum' => array('帖子审核', 'bbs/contentcheck/*', '', '', 'contents'),
	'contentcheck_word' => array('敏感词管理', 'word/manage/*', '', '', 'contents'),
	'contents_user_tag' => array('个人标签', 'u/tag/*', '', '', 'contents'),
	'bbs_recycle' => array('回收站', 'bbs/recycle/*', '', '', 'contents'),

	'bbs_configbbs' => array('论坛设置', 'bbs/configbbs/*', '', '', 'bbs'),
	'bbs_setforum' => array('版块管理', 'bbs/setforum/*', '', '', 'bbs'),
	'bbs_setbbs' => array('功能细节', 'bbs/setbbs/*', '', '', 'bbs'),
	

	'design_page' => array('页面管理', 'design/page,portal/*', '', '', 'design'),
	'design_component' => array('模块模板', 'design/component/*', '', '', 'design'),
	'design_module' => array('模块管理', 'design/module,data,property,template/*', '', '', 'design'),
	'design_push' => array('数据管理', 'design/push/*', '', '', 'design'),
	'design_permissions' => array('权限查看', 'design/permissions/*', '', '', 'design'),

	'database_backup' => array('数据库', 'backup/backup/*', '', '', 'data'),
	'cache_m' => array('缓存管理', 'bbs/cache/*', '', '', 'data'),
	'data_hook' => array('Hook管理', 'hook/manage/*', '', '', 'data'),
	'cron_operations' => array('计划任务', 'cron/cron/*', '', '', 'data'),
	'log_manage' => array('管理日志', 'log/manage,loginlog,adminlog/*', '', '', 'data'),

	//'app_album' => array('相册管理', 'app/manage/*?app=album', '', '', 'appcenter'),
	'app_vote' => array('投票管理', 'vote/manage/*', '', '', 'appcenter'),
	'app_medal' => array('勋章管理', 'medal/medal/*', '', '', 'appcenter'),
	'app_task' => array('任务中心', 'task/manage/*', '', '', 'appcenter'),
	'app_punch' => array('每日打卡', 'config/punch/*', '', '', 'appcenter'),
	'app_link' => array('友情链接', 'link/link/*', '', '', 'appcenter'),
	'app_message' => array('消息群发', 'message/manage/send', '', '', 'appcenter'),
	'app_announce' => array('公告管理', 'announce/announce/*', '', '', 'appcenter'),
    
    //'mobile_oauth' => array('第三方帐号接入', 'mobile/oauthuser/*', '', '', 'mobile'),

	//'platform_server' => array('平台首页', 'appcenter/server/run', '', '', 'platform'),
	//'platform_appList' => array('应用中心', 'appcenter/server/appcenter', '', '', 'platform'),
	//'platform_server_check' => array('服务检测', 'appcenter/server/check', '', '', 'platform'),
	'platform_index'   => array('应用管理', 'appcenter,app/app,develop,manage/*', '', '', 'platform'),
	'platform_siteStyle'  => array('模板管理','appcenter/style/*','','','platform'),
	//'platform_upgrade'  => array('在线升级','appcenter/upgrade,fixup/*','','','platform'),



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
