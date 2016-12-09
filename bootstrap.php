<?php

// 版本检查控制
if (version_compare(PHP_VERSION, '5.3.12', '<')) {
    echo '<pre>',
         '您的PHP版本为:', PHP_VERSION, PHP_EOL,
         'phpwind Fans 运行版本不得低于：PHP 5.3.12', PHP_EOL,
         '</pre>';
    exit;
}

// 尝试性代码，兼容部分主机没有加载路径设置导致绝对路径使用报错的情况
if (function_exists('get_include_path') && function_exists('set_include_path')) {
    // 获取系统目前允许的路径
    $includePaths = (array) explode(PATH_SEPARATOR, get_include_path());
    $rootDir = dirname(__FILE__);

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
         '您必须使用Composer包管理设置项目依赖关系，在程序根目录运行以下命令:', PHP_EOL,
         'composer install', PHP_EOL,
         '</pre>';
    exit;
}

require $filename;
