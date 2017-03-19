<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require dirname(__FILE__).'/bootstrap.php';
Wekit::run('install');
