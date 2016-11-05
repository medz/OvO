<?php
defined('WEKIT_VERSION') or exit(403);

return array(
	'config' => array('全局', array()),
	'config_site' => array('站点设置', 'config/config/*', '', '', 'config'),
	'config_register' => array('注册登录', 'config/regist/*', '', '', 'config'),
	'config_attachment' => array('附件相关', 'config/attachment/*', '', '', 'config'),
	'config_watermark' => array('水印设置', 'config/watermark/*', '', '', 'config'),
	'config_notice' => array('消息设置', 'config/notice/*', '', '', 'config'),
	'config_message' => array('提示信息', 'config/message/*', '', '', 'config'),
	'config_email' => array('电子邮件', 'config/email/*', '', '', 'config'),
	'config_pay' => array('在线支付', 'config/pay/*', '', '', 'config'),
	'config_webdata' => array('资料库', array(), '', '', 'config'),
	'config_area' => array('地区库', 'config/areadata/*', '', '', 'config_webdata'),
	'config_school' => array('学校库', 'config/schooldata/*', '', '', 'config_webdata'),
	
);