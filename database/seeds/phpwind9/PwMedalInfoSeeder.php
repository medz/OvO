<?php

use Illuminate\Database\Seeder;

class PwMedalInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $rows = [
            [1, '社区居民', '', 'big/shequjumin.gif', 'icon/shequjumin.gif', '注册用户登录后即可获得此勋章', 1, 1, '', 10, 1, 0, 1, 0],
            [2, '社区明星', '', 'big/shequmingxing.gif', 'icon/shequmingxing.gif', '提高自身活跃度，增加100个粉丝即可获得此勋章', 1, 1, '', 5, 100, 0, 1, 0],
            [3, '最爱沙发', '', 'big/zuiaishafa.gif', 'icon/zuiaishafa.gif', '坐沙发什么的最爽，赶紧去抢100个沙发吧', 1, 1, '', 4, 100, 0, 1, 0],
            [4, '忠实会员', '', 'big/zhongshihuiyuan.gif', 'icon/zhongshihuiyuan.gif', '连续7天登录即可获得此勋章，如连续3天不登录则收回此勋章', 1, 1, '', 1, 7, 3, 1, 0],
            [5, '喜欢达人', '', 'big/xihuandaren.gif', 'icon/xihuandaren.gif', '努力发好帖，获得100个喜欢', 2, 1, '', 6, 100, 0, 1, 0],
            [6, '优秀斑竹', '', 'big/youxiubanzhu.gif', 'icon/youxiubanzhu.gif', '兢兢业业的斑竹，为网站做出了不可磨灭的贡献', 2, 2, '4,5,3', 0, 0, 0, 1, 0],
            [7, '社区劳模', '', 'big/shequlaomo.gif', 'icon/shequlaomo.gif', '劳动最光荣，连续7天发主题，连续3天不发帖则收回此勋章', 2, 1, '8,9,10,11,12,13,14,4,5,3,15,16', 3, 7, 3, 1, 0],
            [8, 'VIP会员', '', 'big/viphuiyuan.gif', 'icon/viphuiyuan.gif', '尊贵的身份象征，网站高级会员', 2, 2, '', 0, 0, 0, 1, 0],
            [9, '原创写手', '', 'big/yuanchuangxieshou.gif', 'icon/yuanchuangxieshou.gif', '做人就要做自己，发表30个主题帖即可获得此勋章', 2, 1, '8,9,10,11,12,13,14,4,5,3,15,16', 7, 30, 0, 1, 0],
            [10, '荣誉会员', '', 'big/rongyuhuiyuan.gif', 'icon/rongyuhuiyuan.gif', '为网站的发展做出卓越贡献的会员', 2, 2, '', 0, 0, 0, 1, 0],
            [11, '追星一族', '', 'big/zhuixingyizu.gif', 'icon/zhuixingyizu.gif', '狂热的追星一族，关注100个用户即可获得', 2, 1, '', 8, 100, 0, 1, 0],
        ];

        foreach ($rows as $key => $item) {
            $rows[$key] = [
                'medal_id' => $item[0],
                'name' => $item[1],
                'path' => $item[2],
                'image' => $item[3],
                'icon' => $item[4],
                'descrip' => $item[5],
                'medal_type' => $item[6],
                'receive_type' => $item[7],
                'medal_gids' => $item[8],
                'award_type' => $item[9],
                'award_condition' => $item[10],
                'expired_days' => $item[11],
                'isopen' => $item[12],
                'vieworder' => $item[13],
            ];
        }

        $this->container->make('db')->table('pw_medal_info')->insert($rows);
    }
}
