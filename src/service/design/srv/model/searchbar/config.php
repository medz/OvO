<?php

return array(
    'model' => 'searchbar',
    'name' => '搜索条',
    'type' => 'other',
    'refresh' => false,
    'tab' => array('title', 'style', 'property', 'delete'),
    'sign' => array(
        array('{html|html}', '搜索条', 'html'),
    ),
    'standardSign' => array('sTitle' => '{html}', 'sUrl' => '', 'sFromId' => '', 'sIntro' => ''),
    'special' => array(
    ),

    'normal' => array(
        'html' => array('textarea', '搜索条', '', 'searchbar|html', ''),
    ),
);
