<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * pw后台应用配置
 */

return [
    'directory'  => '../conf/windid/directory.php',
    'components' => ['resource' => 'CONF:windid.components.php'],

    'web-apps' => [
        'windidadmin' => [
            'root-path' => 'APPS:admin',
            'filters'   => [
                'default' => [
                    'class' => 'ADMIN:controller.filter.AdminDefaultFilter',
                ],
                'csrf' => [
                    'class'   => 'LIB:filter.PwCsrfTokenFilter',
                    'pattern' => '~(appcenter/app/upload)',
                ],
            ],
            'modules' => [
                'pattern' => [
                    'controller-path' => 'APPS:{m}.admin',
                    'template-path'   => 'TPL:{m}.admin',
                    'compile-path'    => 'DATA:compile.template.windidserver',
                ],
                'default' => [
                    'controller-path'   => 'ADMIN:controller',
                    'controller-suffix' => 'Controller',
                    'error-handler'     => 'ADMIN:controller.MessageController',
                    'template-path'     => 'TPL:admin',
                    'compile-path'      => 'DATA:compile.template.windidserver',
                    'theme-package'     => 'THEMES:',
                ],
                'windidadmin' => [
                    'controller-path' => 'APPS:windidadmin',
                    'template-path'   => 'TPL:windidadmin',
                    'compile-path'    => 'DATA:compile.template.windidserver',
                ],
                'appcenter' => [
                    'controller-path' => 'SRC:applications.appcenter.admin',
                    'template-path'   => 'TPL:appcenter.admin',
                    'compile-path'    => 'DATA:compile.template',
                ],
                'app' => [
                    'controller-path' => 'SRC:extensions.{app}.admin',
                    'template-path'   => 'SRC:extensions.{app}.template.admin',
                    'compile-path'    => 'DATA:compile.template.{app}',
                ],
            ],
        ],
    ],
];
