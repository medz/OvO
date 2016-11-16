<?php

// error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', true);
error_reporting(E_ALL);
define('WIND_DEBUG', 3);

require dirname(__FILE__).'/bootstrap.php';

Wekit::run('phpwind');
