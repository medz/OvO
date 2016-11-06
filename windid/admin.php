<?php

error_reporting(E_ERROR | E_PARSE);

require dirname(__DIR__).'/bootstrap.php';

Wekit::run('windidadmin', array('router' => array()));
