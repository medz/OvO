<?php
/*
return array(
    'dsn' => 'mysql:host=localhost;dbname=nextwind10;port=3306',
    'user' => 'root',
    'pwd' => '',
    'charset' => 'utf8',
    'tableprefix' => 'pw_windid_',
    'engine' => 'MyISAM',
);
*/

$database = include dirname(__DIR__).'/database.php';
$database['tableprefix'] .= 'windid_';

return $database;
