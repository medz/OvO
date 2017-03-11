<?php

/*
|--------------------------------------------------------------------------
| 定义开始时间
|--------------------------------------------------------------------------
|
| 定义一个开始常量，以便统计程序开始运行的时间，
| 主要作用，记录开始运行的时间到结束时间。用于计算整个程序的运行效率。
|
*/

define('WIND_START', microtime(true));

/*
|-------------------------------------------------------------------------
| 开发框架信息
|-------------------------------------------------------------------------
|
| 主要用于开发过程中，多一次文件判断不会影响系统性能。
|
*/

$frameworkAutoloadFile = dirname(__FILE__).'/windframework/vendor/autoload.php';
if (file_exists($frameworkAutoloadFile) && is_file($frameworkAutoloadFile)) {
    require $frameworkAutoloadFile;
}

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

$filename = dirname(__FILE__).'/vendor/autoload.php';
if (!file_exists($filename) || !is_file($filename)) {
    echo '<pre>',
         '您必须使用Composer包管理设置项目依赖关系，在程序根目录运行以下命令:', PHP_EOL,
         'composer install', PHP_EOL,
         '</pre>';
    exit;
}

require $filename;

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new phpwind Fans application instance
| which serves as the "glue" for all the components of phpwind Fans, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Medz\Wind\Application(
    realpath(dirname(__FILE__))
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton('phpwind9', function () {
    return function ($name = 'phpwind', array $components = []) {
        Wekit::run($name, $components);
    };
});

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
