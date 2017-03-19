<?php
/**
 * seo菜单扩展配置文件.
 *
 * 配置项说明：
 * array(
 * 'bbs'=> =====模式/菜单项(论坛)
 * 		  array('title' => '', ====模式名
 * 					'url' => '',   ====模式控制器url
 * 					'page' => 		===模式下的页面配置
 * 								array('index' => ====论坛首页
 * 										 array('default' => '',  =====论坛首页默认seo数据
 * 												 'code' => '' =====论坛首页可使用代码（占位符）
 * )
 * )
 */
return [
    'bbs' => [
        'title' => '论坛',
        'url'   => 'seo/manage/bbs',
        'page'  => [
            'forumlist' => [
                'title'   => '版块导航页',
                'default' => [
                    'title'       => '{sitename}',
                    'description' => '',
                    'keywords'    => '',
                ],
                'code' => ['{sitename}'],
            ],
            'new' => [
                'title'   => '本站新帖',
                'default' => [
                    'title'       => '本站新帖 - 第{page}页 - {sitename}',
                    'description' => '【{sitename}】中的最新帖子列表',
                    'keywords'    => '',
                ],
                'code' => ['{sitename}', '{page}'],
            ],
            'thread' => [
                'title'   => '帖子列表页',
                'default' => [
                    'title'       => '{classification} - {forumname} - 第{page}页 - {sitename}',
                    'description' => '{forumdescription}',
                    'keywords'    => '',
                ],
                'code' => [
                    '{sitename}', '{forumname}', '{forumdescription}', '{classification}',
                ],
            ],
            'read' => [
                'title'   => '帖子阅读页',
                'default' => [
                    'title'       => '{title} - {forumname} - 第{page}页 - {sitename}',
                    'description' => '{description}',
                    'keywords'    => '',
                ],
                'code' => [
                    '{sitename}', '{forumname}', '{title}', '{description}', '{tags}', '{page}',
                ],
            ],
        ],
    ],
    'area' => [
        'title' => '门户',
        'url'   => 'seo/manage/area',
        'page'  => [
            'index' => [
                'title'   => '首页',
                'default' => [
                    'title'       => '{sitename}',
                    'description' => '',
                    'keywords'    => '',
                ],
                'code' => ['{sitename}'],
            ],
            'custom' => [
                'title'   => '自定义页面',
                'default' => [
                    'title'       => '{sitename}',
                    'description' => '',
                    'keywords'    => '',
                ],
                'code' => ['{sitename}', '{pagename}'],
            ],
        ],
    ],
    'like' => [
        'title' => '喜欢',
        'url'   => 'seo/manage/like',
        'page'  => [
            'hot' => [
                'title'   => '热门喜欢',
                'default' => [
                    'title'       => '热门喜欢-{sitename}',
                    'description' => '【{sitename}】中大家最喜欢的帖子',
                    'keywords'    => '',
                ],
                'code' => [
                    '{sitename}',
                ],
            ],
        ],
    ],
    'topic' => [
        'title' => '话题',
        'url'   => 'seo/manage/topic',
        'page'  => [
            'hot' => [
                'title'   => '热门话题',
                'default' => [
                    'title'       => '热门话题-{sitename}',
                    'description' => '【{sitename}】中大家讨论最多的话题',
                    'keywords'    => '',
                ],
                'code' => [
                    '{sitename}',
                ],
            ],
        ],
    ],
];
