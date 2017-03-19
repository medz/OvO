<?php
/**
 * 应用中心本地化管理配置信息.
 *
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 *
 * @link http://www.phpwind.com
 *
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */

return [
    //'url' => 'https://github.com/downloads/phpwind/windframework/{appUrl}',
    'url'      => 'http://open.phpwind-inc.com/attachment/{appUrl}',
    'tmp_dir'  => 'DATA:tmp',
    'log_dir'  => 'DATA:tmp',
    'manifest' => 'Manifest.xml',

    'install-type' => [
        'app' => [
            'class'   => 'APPCENTER:service.srv.do.PwInstall',
            'message' => '默认应用安装',
            'step'    => [
                'after' => [
                    ['method' => 'registeApplication', 'message' => 'APPCENTER:install.step.registeApplication'],
                    ['method' => 'registeHooks', 'message' => 'APPCENTER:install.step.registeHooks'],
                    [
                        'method'  => 'registeInjectServices',
                        'message' => 'APPCENTER:install.step.registeInjectServices', ],
                    ['method' => 'registeData', 'message' => 'APPCENTER:install.step.registeData'],
                    ['method' => 'afterInstall', 'message' => 'APPCENTER:install.step.afterInstall'], ],
                'before' => [['method' => 'install', 'message' => 'APPCENTER:install.step.install']], ], ],

        'style' => [
            'class'   => 'APPCENTER:service.srv.do.PwStyleInstall',
            'message' => '风格安装',
            'step'    => [
                'after' => [
                    ['method' => 'registeApplication', 'message' => 'APPCENTER:install.step.registeStyle'],
                    ['method' => 'afterInstall', 'message' => 'APPCENTER:install.step.movePack'],
                ],
                'before' => [['method' => 'install', 'message' => 'APPCENTER:install.step.install']],
            ],

    ], ],

    'installation-service' => [
        'nav_main'   => ['class' => PwNavInstall::class, 'message' => 'APPCENTER:install.nav.main'],
        'nav_bottom' => ['class' => PwNavInstall::class, 'message' => 'APPCENTER:install.nav.bottom', 'method' => 'bottom'],
        'nav_my'     => ['class' => PwNavInstall::class, 'message' => 'APPCENTER:install.nav.my', 'method' => 'my'],
    ],

    'style-type' => [
        // 别名 => array('名称', '相对于THEMES:目录', '预览地址')
        'site'   => ['整站模板', 'site', ''],
        'space'  => ['个人空间', 'space', 'space/index/run'],
        'forum'  => ['版块模板', 'forum', 'bbs/thread/run'],
        'portal' => ['门户模板', 'portal/appcenter', ''],
    ],
];
