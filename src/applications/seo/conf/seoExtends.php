<?php
/**
 * seo菜单扩展配置文件
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
return array(
	'bbs' => array(
		'title' => '论坛',
		'url' => 'seo/manage/bbs',
		'page' => array(
			'forumlist' => array(
				'title' => '版块导航页',
				'default' => array(
					'title' => '{sitename}',
					'description' => '',
					'keywords' => ''
				),
				'code' => array('{sitename}')
			),
			'new' => array(
				'title' => '本站新帖',
				'default' => array(
					'title' => '本站新帖 - 第{page}页 - {sitename}',
					'description' => '【{sitename}】中的最新帖子列表',
					'keywords' => ''
				),
				'code' => array('{sitename}', '{page}')
			),
			'thread'=> array(
				'title' => '帖子列表页',
				'default' => array(
					'title' => '{classification} - {forumname} - 第{page}页 - {sitename}',
					'description' => '{forumdescription}',
					'keywords' => ''
				),
				'code' => array(
					'{sitename}', '{forumname}', '{forumdescription}', '{classification}'
				)
			),
			'read'=> array(
				'title' => '帖子阅读页',
				'default' => array(
					'title' => '{title} - {forumname} - 第{page}页 - {sitename}',
					'description' => '{description}',
					'keywords' => ''
				),
				'code' => array(
					'{sitename}', '{forumname}', '{title}', '{description}', '{tags}', '{page}'
				)
			)
		)
	),
	'area' => array(
		'title' => '门户',
		'url' => 'seo/manage/area',
		'page' => array(
			'index' => array(
				'title' => '首页',
				'default' => array(
					'title' => '{sitename}',
					'description' => '',
					'keywords' => ''
				),
				'code' => array('{sitename}')
			),
			'custom' => array(
				'title' => '自定义页面',
				'default' => array(
					'title' => '{sitename}',
					'description' => '',
					'keywords' => ''
				),
				'code' => array('{sitename}', '{pagename}')
			)
		)
	),
	'like' => array(
		'title' => '喜欢',
		'url' => 'seo/manage/like',
		'page' => array(
			'hot' => array(
				'title' => '热门喜欢',
				'default' => array(
					'title' => '热门喜欢-{sitename}',
					'description' => '【{sitename}】中大家最喜欢的帖子',
					'keywords' => ''
				),
				'code' => array(
					'{sitename}'
				)
			)
		)
	),
	'topic' => array(
		'title' => '话题',
		'url' => 'seo/manage/topic',
		'page' => array(
			'hot' => array(
				'title' => '热门话题',
				'default' => array(
					'title' => '热门话题-{sitename}',
					'description' => '【{sitename}】中大家讨论最多的话题',
					'keywords' => ''
				),
				'code' => array(
					'{sitename}'
				)
			)
		)
	)
);