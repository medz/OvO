<?php

error_reporting(E_ERROR | E_PARSE);

require dirname(__DIR__).'/bootstrap.php';

$components = array('router' => array());
Wekit::run('windid', $components);
