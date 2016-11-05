<?php
error_reporting(E_ERROR | E_PARSE);
require '.././src/wekit.php';
$components = array('router' => array());
Wekit::run('windid', $components);
?>