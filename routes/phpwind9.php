<?php

// index.php and read.php
Route::any('/old/{file?}', function ($file = 'index.php') {
    Wekit::run($file, 'phpwind');
})->where('file', 'index.php|read.php');

// admin.php
Route::any('/old/admin.php', function () {
    Wekit::run('admin.php', 'pwadmin', [
        'router' => [
            'config' => [
                'module' => ['default-value' => 'default'],
                'routes' => [
                    'admin' => [
                        'class'   => PwAdminRoute::class,
                        'default' => true,
                    ],
                ],
            ],
        ],
    ]);
});

// windid.php
Route::any('/old/windid.php', function () {
    Wekit::run('windid.php', 'windidnotify', ['router' => []]);
});

// install.php
Route::any('/old/install.php', function () {
    Wekit::run('install.php', 'install');
});

// windid/index.php
Route::any('/old/windid/{filename?}', function () {
    Wekit::run('windid/index.php' ,'windid', $components);
})->where('filename', 'index.php', ['router' => []]);

// windid/admin.php
Route::any('/old/windid/admin.php', function () {
    Wekit::run('windid/admin.php', 'windidadmin', ['router' => []]);
});

Route::any('/old/{filename}.{ext}', function (Illuminate\Filesystem\Filesystem $filesystem, $filename, $ext) {
    $filename = base_path(sprintf('phpwind9/%s.%s', $filename, $ext));

    $alias = [
        'css' => 'text/css',
        'js'  => 'text/javascript',
        'xml' => 'application/xml',
    ];

    $headers = [
        'Content-Type' => $alias[$ext] ?? $filesystem->mimeType($filename),
    ];

    return response()->file($filename, $headers);
})->where([
    'filename' => '.*',
]);
