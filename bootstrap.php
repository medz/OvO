<?php

// 尝试性代码，兼容部分主机没有加载路径设置导致绝对路径使用报错的情况
if (function_exists('get_include_path') && function_exists('set_include_path')) {
    // 获取系统目前允许的路径
    $includePaths = (array) explode(PATH_SEPARATOR, get_include_path());
    $rootDir = __DIR__;

    // 如果没有设置在include_path中。
    if (!in_array($rootDir, $includePaths)) {
        // 加入root的完整路径
        array_push($includePaths, $rootDir);

        // 设置include_path
        @set_include_path(implode(PATH_SEPARATOR, $includePaths));
    }
}

// 开发框架信息
$frameworkAutoloadFile = dirname(__FILE__).'/windframework/vendor/autoload.php';
if (file_exists($frameworkAutoloadFile) && is_file($frameworkAutoloadFile)) {
    require $frameworkAutoloadFile;
}

$filename = dirname(__FILE__).'/vendor/autoload.php';
if (!file_exists($filename) || !is_file($filename)) {
    echo '<pre>',
         'You must set up the project dependencies, run the following commands:', PHP_EOL,
         'curl -sS https://getcomposer.org/installer | php', PHP_EOL,
         'php composer.phar install', PHP_EOL,
         '</pre>';
    exit;
}

require $filename;
