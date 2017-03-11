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
return array(
    'support-tags' => array(
        'design' => array(
            'tag'      => 'design',
            'compiler' => PwTemplateCompilerDesign::class,
        ),
        'portal' => array(
            'tag'      => 'pw',
            'compiler' => PwTemplateCompilerPortal::class,
            'pattern'  => '/\<pw-start\/>(.+)<pw-end\/>/isU',
        ),
        'page' => array(
            'tag'      => 'page',
            'compiler' => PwTemplateCompilerPage::class,
        ),
        'component' => array(
            'tag'      => 'component',
            'compiler' => PwTemplateCompilerComponent::class,
        ),
        'hook' => array(
            'tag'      => 'hook',
            'compiler' => PwTemplateCompilerHook::class,
        ),
        'config' => array(
            'tag'      => 'config',
            'compiler' => PwTemplateCompilerConfig::class,
            'pattern'  => '/{@C:[^\}]*}/i',
        ),
        'themeUrl' => array(
            'tag'      => 'themeUrl',
            'compiler' => PwTemplateCompilerThemeUrl::class,
            'pattern'  => '/{@theme:[^\}]*}/i',
        ),
        'url' => array(
            'tag'      => 'url',
            'compiler' => PwTemplateCompilerUrlCreater::class,
            'pattern'  => '/{@url:[^\}]*}/i',
        ),
        'segment' => array(
            'tag'      => 'segment',
            'compiler' => PwTemplateCompilerSegment::class,
        ),
        'advertisement' => array(
            'tag'      => 'advertisement',
            'compiler' => PwTemplateCompilerAdvertisement::class,
        ),
        'csrftoken' => array(
            'tag'      => 'csrftoken',
            'compiler' => PwTemplateCompilerCsrftoken::class,
            'pattern'  => '/<\/form>/i',
        ),
    ),
);
