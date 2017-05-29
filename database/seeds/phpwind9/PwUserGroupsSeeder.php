<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwUserGroup;

class PwUserGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [
            ['gid' => 1, 'name' => '会员', 'type' => 'default', 'image' => '0.gif', 'points' => 0],
            ['gid' => 2, 'name' => '游客', 'type' => 'default', 'image' => '0.gif', 'points' => 0],
            ['gid' => 3, 'name' => '管理员', 'type' => 'system', 'image' => '20.gif', 'points' => 0],
            ['gid' => 4, 'name' => '总版主', 'type' => 'system', 'image' => '19.gif', 'points' => 0],
            ['gid' => 5, 'name' => '论坛版主', 'type' => 'system', 'image' => '18.gif', 'points' => 0],
            ['gid' => 6, 'name' => '禁止发言', 'type' => 'default', 'image' => '0.gif', 'points' => 0],
            ['gid' => 7, 'name' => '未验证会员', 'type' => 'default', 'image' => '0.gif', 'points' => 0],
            ['gid' => 8, 'name' => '贫民', 'type' => 'member', 'image' => '1.gif', 'points' => 0],
            ['gid' => 9, 'name' => '新手', 'type' => 'member', 'image' => '3.gif', 'points' => 50],
            ['gid' => 10, 'name' => '侠客', 'type' => 'member', 'image' => '5.gif', 'points' => 100],
            ['gid' => 11, 'name' => '骑士', 'type' => 'member', 'image' => '6.gif', 'points' => 300],
            ['gid' => 12, 'name' => '圣骑士', 'type' => 'member', 'image' => '8.gif', 'points' => 600],
            ['gid' => 13, 'name' => '精灵王', 'type' => 'member', 'image' => '10.gif', 'points' => 1000],
            ['gid' => 14, 'name' => '风云使者', 'type' => 'member', 'image' => '12.gif', 'points' => 5000],
            ['gid' => 15, 'name' => '光明使者', 'type' => 'member', 'image' => '13.gif', 'points' => 10000],
            ['gid' => 16, 'name' => 'VIP', 'type' => 'special', 'image' => '13.gif', 'points' => 0],
        ];

        foreach ($rows as $item) {
            PwUserGroup::create($item);
        }
    }
}
