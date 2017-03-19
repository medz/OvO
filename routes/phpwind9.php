<?php

$兼容pw9 = function ($file) {
    $_SERVER['DOCUMENT_URI'] = '/old/'.$file;
    $_SERVER['SCRIPT_NAME'] = $_SERVER['DOCUMENT_URI'];
    $_SERVER['PHP_SELF'] = $_SERVER['DOCUMENT_URI'];
    $_SERVER['SCRIPT_FILENAME'] = public_path('old/'.$file);
};

// index.php and read.php
Route::any('/old/{file?}', function ($file = 'index.php') use ($兼容pw9) {
    $兼容pw9($file);
    Wekit::run('phpwind');
})->where('file', 'index.php|read.php');

// admin.php
Route::any('/old/admin.php', function () use ($兼容pw9) {
    $兼容pw9('admin.php');
    Wekit::run('pwadmin', [
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
