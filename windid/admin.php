<?php

error_reporting(E_ERROR | E_PARSE);
// ini_set('display_errors', true);
// error_reporting(E_ALL);
// define('WIND_DEBUG', 3);

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require dirname(__DIR__).'/bootstrap.php';


/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$phpwind9 = $app->make('phpwind9');
$phpwind9('windidadmin', ['router' => []]);
