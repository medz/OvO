<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 组件配置文件
 *
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return [
    'error' => [
        'path' => PwErrorHandler::class,
    ],
    'pwWidget' => [
        'path'  => PwWidget::class,
        'scope' => 'singleton',
    ],
    'pwComponent' => [
        'path'   => PwComponent::class,
        'scope'  => 'singleton',
        'config' => ['resource' => 'CONF:pwcomponents.php'],
    ],
    'security' => [
        'path'  => WindXxtea::class,
        'scope' => 'singleton',
    ],
    'windLogger' => [
        'constructor-args' => ['0' => ['value' => 'DATA:log'], '1' => ['value' => '2'], '2' => ['value' => 10000]],
    ],
    'router' => [
        'config' => [
            'routes' => [
                'pw' => [
                    'class'   => PwRoute::class,
                    'default' => true,
                ],
            ],
        ],
    ],
    'windView' => [
        'config' => ['themePackPattern' => '{pack}.{theme}.template'],
    ],
    'template' => [
        'config' => ['resource' => 'CONF:compiler.php'],
    ],
    'i18n' => [
        'config' => ['path' => 'SRC:i18n', 'suffix' => '.lang'],
    ],
    'db' => [
        'config' => ['resource' => 'CONF:database.php'],
    ],
    'windToken' => [
        'path'  => PwCsrfToken::class,
        'scope' => 'singleton',
    ],
    'windCookie' => [
        'path'  => WindNormalCookie::class,
        'scope' => 'singleton',
    ],
    'httptransfer' => [
        'path'  => WindHttpSocket::class,
        'scope' => 'prototype',
    ],
    'storage' => [
        'path'  => PwStorageLocal::class,
        'scope' => 'singleton',
    ],
    'localStorage' => [
        'path'  => PwStorageLocal::class,
        'scope' => 'singleton',
    ],
    'fileCache' => [
        'path' => PwFileCache::class,
// 		'path' => 'WIND:cache.strategy.WindFileCache',
        'scope'  => 'application',
        'config' => [
            'dir'           => 'DATA:cache',    //缓存文件存放的目录,注意可读可写
            'suffix'        => 'txt',    //缓存文件的后缀,默认为txt后缀
            'dir-level'     => '0',    //缓存文件存放目录的子目录长度,默认为0不分子目录
            'security-code' => '',    //继承自AbstractWindCache,安全码配置
            'key-prefix'    => 'pw_',     //继承自AbstractWindCache,缓存key前缀
            'expires'       => '0',    //继承自AbstractWindCache,缓存过期时间配置
        ],
    ],
];
