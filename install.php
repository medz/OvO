<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require './src/wekit.php';
Wekit::run('install');