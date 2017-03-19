<?php

return [
    'model'   => 'html',
    'name'    => '自定义html',
    'type'    => 'other',
    'refresh' => false,
    'tab'     => ['title', 'style', 'property', 'delete'],
    'sign'    => [
        ['{html|html}', '自定义html', 'html'],
    ],
    'standardSign' => ['sTitle' => '{html}', 'sUrl' => '', 'sFromId' => '', 'sIntro' => ''],
    'special'      => [
    ],

    'normal' => [
        'html' => ['textarea', '自定义html', '限10000字节', '', ''],
    ],
];
