<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy: gao.wanggao $>
 * <note>
 * 1.type为'bbs''user''space''other''api'之一
 * 2.refresh 是否需要数据更新
 * 3.sign为自定义标签 array('标签', '名称','字段')
 * 4.standardSign指定的格式为array('列表标题','url','来源ID','简介')
 * 5.special里的属性可以注释，但不能修改
 * 6.normal为自定义 的设置属性 array('表单类型','标题','说明','多选值(如为变量名可选为以array还是html显示)','其它属性(类型long为长表单,short为短表单,multiple可多选)')
 * 7.表单类型为text  select   radio checkbox textarea 中一种
 * </note>.
 *
 * @author xiaoxia.xu <xiaoxia.xuxx@aliyun-inc.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.windframework.com
 *
 * @version $Id: config.php 23959 2013-01-17 08:36:09Z gao.wanggao $
 */
return [
    'model'   => 'user',
    'name'    => '用户',
    'type'    => 'user',
    'refresh' => true,
    'sign'    => [
        ['{uid}', '用户ID', 'uid'],
        ['{username}', '用户名字', 'username'],
        ['{url}', '空间链接', 'url'],
        ['{avatar_s}', '用户小头像(50*50)', 'smallavatar'],
        ['{avatar_m}', '用户中头像(120*120)', 'middleavatar'],
        ['{avatar_b}', '用户大头像(200*200)', 'bigavatar'],
        ['{regdate}', '注册时间', 'regdate'],
        ['{lastvisit}', '访问时间', 'lastvisit'],
        ['{posts}', '发帖数', 'posts'],
        ['{digests}', '精华帖数', 'digests'],
        ['{compositePoint}', '综合积分数', 'compositePoint'],
        ['{realname}', '真实姓名', 'realname'],
        ['{sex}', '性别', 'sex'],
        ['{birthYear}', '出生年份', 'birthYear'],
        ['{birthMonth}', '出生月份', 'birthMonth'],
        ['{birthDay}', '出生日期', 'birthDay'],
        ['{locate_province}', '居住省份', 'locate_province'],
        ['{locate_city}', '居住地', 'locate_city'],
        ['{locate_area}', '居住县', 'locate_area'],
        ['{home_province}', '出生省份', 'home_province'],
        ['{home_city}', '出生地', 'home_city'],
        ['{home_area}', '出生县', 'home_area'],
        ['{homepage}', '个人主页', 'homepage'],
        ['{profile}', '个人简介', 'profile'],
        ['{alipay}', '支付宝', 'alipay'],
        ['{mobile}', '手机号码', 'mobile'],
        ['{telphone}', '电话号码', 'telphone'],
        ['{address}', '邮寄地址', 'address'],
        ['{zipcode}', '邮编', 'zipcode'],
        ['{email}', '邮箱', 'email'],
        ['{aliww}', '阿里旺旺', 'aliww'],
        ['{QQ}', 'QQ', 'qq'],
        ['{MSN}', 'MSN', 'msn'],

    ],

    'standardSign' => [
        'sTitle'  => '{username}',
        'sUrl'    => '{url}',
        'sFromId' => '{uid}',
        'sIntro'  => '{profile}',
    ],

    //以下为查询及显示条件
    'special' => [
        'limit'   => ['text', '显示条数', '默认10条', '', 'short'],
        'timefmt' => ['select', '时间格式', '', ['m-d' => '04-26', 'Y-m-d' => '2012-04-2', 'Y-m-d H:i:s' => '2012-04-26 11:30', 'H:i:s' => '11:30:59', 'n月j日' => '4月26日', 'y年n月j日' => '12年4月26日', 'auto' => '几天前']],
        'isblank' => ['radio', '链接打开方式', '', ['0' => '当前窗口', '1' => '新窗口'], ''],
    ],

    'normal' => [
        'usernames' => ['text', '用户名', '多个用户名之间采用空格隔开', '', 'long'],
        'gid'       => ['select', '用户组', '', 'gidOptions|html', ''],
        'gender'    => ['checkbox', '性别', '', ['0' => '男性', '1' => '女性']],
        'hometown'  => ['html', '家乡', '', '', 'user_area|hometown'],
        'location'  => ['html', '居住地', '', '', 'user_area|location'],
        'orderby'   => [
            'select',
            '用户排序方式',
            '',
            [
                '1' => '系统推荐排序',
                '2' => '按主题数倒序',
                '3' => '按发帖时间倒序',
                /*'4' => '按被喜欢数倒序',*/
                '5' => '按注册时间倒序',
                '6' => '按访问时间倒序',
            ],
        ],
    ],
];
