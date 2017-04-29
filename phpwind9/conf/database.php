<?php
    
if (config('database.default') !== 'mysql') {
    throw new \Exception('phpwind 9 Database driver only "MySQL".');
}

return [
    'dsn' => sprintf(
        'mysql:host=%s;dbname=%s;port=%s',
        config('database.connections.mysql.host'),
        config('database.connections.mysql.database'),
        config('database.connections.mysql.port')
    ),
    'user' => config('database.connections.mysql.username'),
    'pwd' => config('database.connections.mysql.password'),
    'charset' => config('database.connections.mysql.charset'),
    'tableprefix' => config('database.connections.mysql.prefix').'pw_',
    'engine' => 'InnoDB',
];
