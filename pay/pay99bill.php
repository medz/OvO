<?php
error_reporting(0);
define('BOOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
require '.././src/wekit.php';
Wekit::run('phpwind');