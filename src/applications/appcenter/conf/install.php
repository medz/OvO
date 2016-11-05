<?php
/**
 * 应用中心本地化管理配置信息
 * 
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

return array(
	//'url' => 'https://github.com/downloads/phpwind/windframework/{appUrl}',
	'url' => 'http://open.phpwind-inc.com/attachment/{appUrl}', 
	'tmp_dir' => 'DATA:tmp', 
	'log_dir' => 'DATA:tmp', 
	'manifest' => 'Manifest.xml', 
	
	'install-type' => array(
		'app' => array(
			'class' => 'APPCENTER:service.srv.do.PwInstall', 
			'message' => '默认应用安装', 
			'step' => array(
				'after' => array(
					array('method' => 'registeApplication', 'message' => 'APPCENTER:install.step.registeApplication'), 
					array('method' => 'registeHooks', 'message' => 'APPCENTER:install.step.registeHooks'), 
					array(
						'method' => 'registeInjectServices', 
						'message' => 'APPCENTER:install.step.registeInjectServices'), 
					array('method' => 'registeData', 'message' => 'APPCENTER:install.step.registeData'), 
					array('method' => 'afterInstall', 'message' => 'APPCENTER:install.step.afterInstall')), 
				'before' => array(array('method' => 'install', 'message' => 'APPCENTER:install.step.install')))), 
		
		'style' => array(
			'class' => 'APPCENTER:service.srv.do.PwStyleInstall', 
			'message' => '风格安装',
			'step' => array(
				'after' => array(
					array('method' => 'registeApplication', 'message' => 'APPCENTER:install.step.registeStyle'),
					array('method' => 'afterInstall', 'message' => 'APPCENTER:install.step.movePack'),
				),
				'before' => array(array('method' => 'install', 'message' => 'APPCENTER:install.step.install'))
			),
				
	)), 
	
	'installation-service' => array(
		'nav_main' => array('class' => 'SRV:nav.srv.PwNavInstall', 'message' => 'APPCENTER:install.nav.main'),
		'nav_bottom' => array('class' => 'SRV:nav.srv.PwNavInstall', 'message' => 'APPCENTER:install.nav.bottom', 'method' => 'bottom'),
		'nav_my' => array('class' => 'SRV:nav.srv.PwNavInstall', 'message' => 'APPCENTER:install.nav.my', 'method' => 'my'),
	), 
	
	'style-type' => array(
		// 别名 => array('名称', '相对于THEMES:目录', '预览地址')
		'site' => array('整站模板', 'site', ''), 
		'space' => array('个人空间', 'space', 'space/index/run'),
		'forum' => array('版块模板', 'forum', 'bbs/thread/run'),
		'portal' => array('门户模板', 'portal/appcenter', '')
	)
);