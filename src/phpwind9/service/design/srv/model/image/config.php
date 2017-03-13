<?php

return [
    'model'   => 'image',
    'name'    => '图片',
    'type'    => 'other',
    'refresh' => false,

    'tab'          => ['title', 'style', 'property', 'delete'],
    'standardSign' => ['sTitle' => '{image}', 'sUrl' => '{url}', 'sFromId' => '', 'sIntro' => '{intro}'],
    'special'      => [
        'isblank' => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],
    'sign' => [
        ['{image}', '图片', 'image'],
        ['{url}', '链接地址', 'url'],
        ['{height}', '图片高', 'height'],
        ['{width}', '图片宽', 'width'],
        ['{intro}', '图片描述', 'intro'],
    ],

    'normal' => [
        'image'  => ['html', '图片', '', '', 'image|image'],
        'url'    => ['text', '图片链接地址', '以http://开头', '', 'long'],
        'height' => ['text', '图片高', '', '', ''],
        'width'  => ['text', '图片宽', '', '', ''],
        'intro'  => ['text', '图片描述', '', '', 'long'],
    ],
];
