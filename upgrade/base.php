<?php

require dirname(__DIR__).'/bootstrap.php';

$tag = 'v1.0.8';
$repositorie = 'https://github.com/medz/phpwind';

$instance = new Medz\Wind\Upgrade\Repositorie($repositorie, $tag);
