<?php
error_reporting(0);
define('BOOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
@header("Content-Type:text/html; charset=utf-8");
define('SCR', 'aCloud_index');
define('WIND_DEBUG', 0);
require_once ('.././src/wekit.php');
Wekit::init('acloud');
$front = Wind::application('acloud', WEKIT_PATH . 'aCloud/aCloudConfig.php');
// $front->createApplication();

Wekit::createapp('acloud');
$config = include WEKIT_PATH . '../conf/application/default.php';
Wekit::setV('charset', $config['web-apps']['default']['charset']);

require_once (WEKIT_PATH . 'aCloud/aCloud.php');
$router = new ACloudRouter();
$router->run();