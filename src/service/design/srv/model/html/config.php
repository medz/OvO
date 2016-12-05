<?php

return array(
    'model'   => 'html',
    'name'    => '自定义html',
    'type'    => 'other',
    'refresh' => false,
    'tab'     => array('title', 'style', 'property', 'delete'),
    'sign'    => array(
        array('{html|html}', '自定义html', 'html'),
    ),
    'standardSign' => array('sTitle' => '{html}', 'sUrl' => '', 'sFromId' => '', 'sIntro' => ''),
    'special'      => array(
    ),

    'normal' => array(
        'html' => array('textarea', '自定义html', '限10000字节', '', ''),
    ),
);
