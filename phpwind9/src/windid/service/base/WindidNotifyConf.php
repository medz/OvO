<?php

return [
    '999' => [
        'method'      => 'test',
        'args'        => ['testdata'],
        'alias'       => '通讯测试',
        'description' => '通讯测试接口',
    ],
    '101' => [
        'method'      => 'addUser',
        'args'        => ['uid'],
        'alias'       => '用户  %s  注册',
        'description' => '注册用户',
    ],
    '111' => [
        'method'      => 'synLogin',
        'args'        => ['uid'],
        'alias'       => '%s 同步登录',
        'description' => '同步登录',
    ],
    '112' => [
        'method'      => 'synLogout',
        'args'        => ['uid'],
        'alias'       => ' %s 同步登出',
        'description' => '同步登出',
    ],
    '201' => [
        'method'      => 'editUser',
        'args'        => ['uid', 'changepwd'],
        'alias'       => '修改  %s  用户信息',
        'description' => '编辑用户基本信息(用户名，密码，邮箱，安全问题)',
    ],
    '202' => [
        'method'      => 'editUserInfo',
        'args'        => ['uid'],
        'alias'       => '修改  %s  详细资料',
        'description' => '修改用户详细资料',
    ],
    '203' => [
        'method'      => 'uploadAvatar',
        'args'        => ['uid'],
        'alias'       => '上传  %s  头像',
        'description' => '上传用户头像 ',
    ],
    '211' => [
        'method'      => 'editCredit',
        'args'        => ['uid'],
        'alias'       => '修改  %s  积分',
        'description' => '修改用户积分',
    ],
    '222' => [
        'method'      => 'editMessageNum',
        'args'        => ['uid'],
        'alias'       => '修改  %s  未读消息',
        'description' => '修改未读消息',
    ],
    '301' => [
        'method'      => 'deleteUser',
        'args'        => ['uid'],
        'alias'       => '删除  %s',
        'description' => '删除用户',
    ],
    '402' => [
        'method'      => 'setCredits',
        'args'        => [],
        'alias'       => '修改积分配置',
        'description' => '修改积分配置',
    ],
    '403' => [
        'method'      => 'alterAvatarUrl',
        'args'        => [],
        'alias'       => '修改头像链接',
        'description' => '修改头像链接',
    ],
];
