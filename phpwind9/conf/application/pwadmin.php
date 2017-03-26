<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * pw后台应用配置
 */

return [
    'web-apps' => [
        'pwadmin' => [
            'default-module' => 'default',
            'root-path'      => 'APPS:admin',
            'filters'        => [
                'default' => [
                    'class' => 'ADMIN:controller.filter.AdminDefaultFilter',
                ],
                'develop' => [
                    'class' => 'APPS:pwadmin.service.srv.filter.PwDebugFilter',
                ],
                'csrf' => [
                    'class'   => PwCsrfTokenFilter::class,
                    'pattern' => '~(appcenter/app/upload)', ],
                ],
            'modules' => [
                'pattern' => [
                    'controller-path' => 'APPS:{m}.admin',
                    'template-path'   => 'TPL:{m}.admin',
                    'compile-path'    => 'DATA:compile.template', ],
                'pwadmin' => [
                    'controller-path' => 'APPS:pwadmin',
                    'template-path'   => 'TPL:pwadmin',
                    'compile-path'    => 'DATA:compile.template', ],
                'default' => [
                    'controller-path'   => 'ADMIN:controller',
                    'controller-suffix' => 'Controller',
                    'error-handler'     => 'ADMIN:controller.MessageController',
                    'template-path'     => 'TPL:admin',
                    'compile-path'      => 'DATA:compile.template',
                    'theme-package'     => 'THEMES:', ],
                'app' => [
                    'controller-path' => 'SRC:extensions.{app}.admin',
                    'template-path'   => 'SRC:extensions.{app}.template.admin',
                    'compile-path'    => 'DATA:compile.template.{app}', ],
            ],
        ],
    ],
];
