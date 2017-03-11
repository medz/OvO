<?php

return array(
    'model'   => 'image',
    'name'    => '图片',
    'type'    => 'other',
    'refresh' => false,

    'tab'          => array('title', 'style', 'property', 'delete'),
    'standardSign' => array('sTitle' => '{image}', 'sUrl' => '{url}', 'sFromId' => '', 'sIntro' => '{intro}'),
    'special'      => array(
        'isblank' => array('radio', '链接打开方式', '', array('0' => '当前窗口', '1' => '新窗口'), ''),
    ),
    'sign' => array(
        array('{image}', '图片', 'image'),
        array('{url}', '链接地址', 'url'),
        array('{height}', '图片高', 'height'),
        array('{width}', '图片宽', 'width'),
        array('{intro}', '图片描述', 'intro'),
    ),

    'normal' => array(
        'image'  => array('html', '图片', '', '', 'image|image'),
        'url'    => array('text', '图片链接地址', '以http://开头', '', 'long'),
        'height' => array('text', '图片高', '', '', ''),
        'width'  => array('text', '图片宽', '', '', ''),
        'intro'  => array('text', '图片描述', '', '', 'long'),
    ),
);
