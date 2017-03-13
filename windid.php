<?php

error_reporting(E_ERROR | E_PARSE);

require __DIR__.'/bootstrap.php';

$components = ['router' => []];
Wekit::run('windidnotify', $components);
