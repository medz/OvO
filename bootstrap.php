<?php

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
