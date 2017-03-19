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

Route::any('/old/{filename}.{ext}', function (Illuminate\Filesystem\Filesystem $filesystem, $filename, $ext) {
    $filename = base_path(sprintf('phpwind9/%s.%s', $filename, $ext));

    $alias = [
        'css' => 'text/css',
        'js'  => 'text/javascript',
    ];

    $headers = [
        'Content-Type' => $alias[$ext] ?? $filesystem->mimeType($filename),
    ];

    return response()->file($filename, $headers);
})->where([
    'filename' => '.*',
]);
