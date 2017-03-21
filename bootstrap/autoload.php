<?php

define('LARAVEL_START', microtime(true));

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

require __DIR__.'/../vendor/autoload.php';

/*
|-------------------------------------------------------------------------
| 开发框架信息
|-------------------------------------------------------------------------
|
| 主要用于开发过程中，多一次文件判断不会影响系统性能。
|
*/

$frameworkAutoloadFile = dirname(__DIR__).'/windframework/vendor/autoload.php';
if (file_exists($frameworkAutoloadFile) && is_file($frameworkAutoloadFile)) {
    require $frameworkAutoloadFile;
}
