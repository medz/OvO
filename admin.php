<?php
error_reporting(E_ERROR | E_PARSE);

require './src/wekit.php';

$components = array('router' => array('config' => array('module' => array('default-value' => 'default'), 'routes' => array('admin' => array('class' => 'LIB:route.PwAdminRoute','default' => true)))));
Wekit::run('pwadmin', $components);