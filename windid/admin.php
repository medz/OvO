<?php

error_reporting(E_ERROR | E_PARSE);
require '.././src/wekit.php';

Wekit::run('windidadmin', array('router' => array()));
