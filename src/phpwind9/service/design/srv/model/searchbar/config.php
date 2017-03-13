<?php

return [
    'model'   => 'searchbar',
    'name'    => '搜索条',
    'type'    => 'other',
    'refresh' => false,
    'tab'     => ['title', 'style', 'property', 'delete'],
    'sign'    => [
        ['{html|html}', '搜索条', 'html'],
    ],
    'standardSign' => ['sTitle' => '{html}', 'sUrl' => '', 'sFromId' => '', 'sIntro' => ''],
    'special'      => [
    ],

    'normal' => [
        'html' => ['textarea', '搜索条', '', 'searchbar|html', ''],
    ],
];
