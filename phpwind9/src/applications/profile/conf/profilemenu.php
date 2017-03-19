<?php
/**
 * 个人中心扩展配置
 * profile_tab:  个人中心tab页扩展
 * profile_left: 个人中心左侧菜单扩展.
 */
return [
    /* 个人资料tab页扩展 */
    'profile_tabs' => [
        'profile'   => ['title' => '基本资料', 'url' => 'profile/index/run'],
        'contact'   => ['title' => '联系方式', 'url' => 'profile/index/contact'],
        'work'      => ['title' => '工作经历', 'url' => 'profile/work/run'],
        'education' => ['title' => '教育经历', 'url' => 'profile/education/run'],
        'tag'       => ['title' => '个人标签', 'url' => 'profile/tag/run'],
    ],
    /* 积分TAB */
    'credit_tabs' => [
        'run'      => ['title' => '我的积分', 'url' => 'profile/credit/run'],
        'recharge' => ['title' => '积分充值', 'url' => 'profile/credit/recharge'],
        'log'      => ['title' => '积分日志', 'url' => 'profile/credit/log'],
    ],
    /* 个人中心-左侧菜单扩展 */
    'profile_left' => [
        'profile'  => ['title' => '资料', 'url' => 'profile/index/run', 'tabs' => true],
        'avatar'   => ['title' => '头像', 'url' => 'profile/avatar/run'],
        'secret'   => ['title' => '隐私', 'url' => 'profile/secret/run'],
        'credit'   => ['title' => '积分', 'url' => 'profile/credit/run', 'tabs' => true],
        'right'    => ['title' => '权限', 'url' => 'profile/right/run'],
        'password' => ['title' => '密码安全', 'url' => 'profile/password/run'],
    ],
];
