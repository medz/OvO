<?php
defined('WEKIT_VERSION') or exit(403);
return array(
	'appcenter' => array('应用', array()), 
	'appcenter_test' => array('test', 'appcenter/app/*', '', '', 'appcenter'),
	'appcenter_server_check' => array('服务检测', 'appcenter/server/*?operate=check', '', '', 'appcenter'),
	'appcenter_server' => array('服务中心', 'appcenter/server/*', '', '', 'appcenter'),
	'appcenter_appList' => array('应用中心', 'appcenter/appcenter/*', '', '', 'appcenter'),
	'appcenter_index'   => array('我的应用', 'appcenter/spp/*', '', '', 'appcenter'),
	'appcenter_siteStyle'  => array('我的模板','appcenter/style/*','','','appcenter'),
);
