<?php

defined('WEKIT_VERSION') or exit(403);
/*
 * 视图编译器配置文件
 *
 * @author Qiong Wu <papa0924@gmail.com> 2010-11-2
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2010 phpwind.com
 * @license
 */
return [
    'support-tags' => [
        'design' => [
            'tag'      => 'design',
            'compiler' => PwTemplateCompilerDesign::class,
        ],
        'portal' => [
            'tag'      => 'pw',
            'compiler' => PwTemplateCompilerPortal::class,
            'pattern'  => '/\<pw-start\/>(.+)<pw-end\/>/isU',
        ],
        'page' => [
            'tag'      => 'page',
            'compiler' => PwTemplateCompilerPage::class,
        ],
        'component' => [
            'tag'      => 'component',
            'compiler' => PwTemplateCompilerComponent::class,
        ],
        'hook' => [
            'tag'      => 'hook',
            'compiler' => PwTemplateCompilerHook::class,
        ],
        'config' => [
            'tag'      => 'config',
            'compiler' => PwTemplateCompilerConfig::class,
            'pattern'  => '/{@C:[^\}]*}/i',
        ],
        'themeUrl' => [
            'tag'      => 'themeUrl',
            'compiler' => PwTemplateCompilerThemeUrl::class,
            'pattern'  => '/{@theme:[^\}]*}/i',
        ],
        'url' => [
            'tag'      => 'url',
            'compiler' => PwTemplateCompilerUrlCreater::class,
            'pattern'  => '/{@url:[^\}]*}/i',
        ],
        'segment' => [
            'tag'      => 'segment',
            'compiler' => PwTemplateCompilerSegment::class,
        ],
        'advertisement' => [
            'tag'      => 'advertisement',
            'compiler' => PwTemplateCompilerAdvertisement::class,
        ],
        'csrftoken' => [
            'tag'      => 'csrftoken',
            'compiler' => PwTemplateCompilerCsrftoken::class,
            'pattern'  => '/<\/form>/i',
        ],
    ],
];
