<?php

error_reporting(0);
define('BOOT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

require dirname(__DIR__).'/bootstrap.php';

Wekit::run('phpwind');
