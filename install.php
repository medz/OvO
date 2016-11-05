<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require __DIR__.'/bootstrap.php';
Wekit::run('install');
