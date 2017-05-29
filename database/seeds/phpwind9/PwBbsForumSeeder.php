<?php

use Illuminate\Database\Seeder;

class PwBbsForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_bbs_forum')->insert([
            [
                'fid' => 1,
                'parentid' => 0,
                'type' => 'category',
                'issub' => 0,
                'hassub' => 1,
                'name' => '新分类',
                'descrip' => '',
                'vieworder' => 0,
                'across' => 2,
                'manager' => '',
                'uppermanager' => '',
                'icon' => '',
                'logo' => '',
                'fup' => '',
                'fupname' => '',
                'isshow' => 1,
                'isshowsub' => 0,
                'newtime' => 60,
                'password' => '',
                'allow_visit' => '',
                'allow_read' => '',
                'allow_post' => '',
                'allow_reply' => '',
                'allow_upload' => '',
                'allow_download' => '',
                'created_time' => 0,
                'created_username' => '',
                'created_userid' => 0,
                'created_ip' => 0,
                'style' => '',
            ],
            [
                'fid' => 2,
                'parentid' => 1,
                'type' => 'forum',
                'issub' => 0,
                'hassub' => 0,
                'name' => '新版块',
                'descrip' => '',
                'vieworder' => 0,
                'across' => 2,
                'manager' => '',
                'uppermanager' => '',
                'icon' => '',
                'logo' => '',
                'fup' => 1,
                'fupname' => '新分类',
                'isshow' => 1,
                'isshowsub' => 0,
                'newtime' => 60,
                'password' => '',
                'allow_visit' => '',
                'allow_read' => '',
                'allow_post' => '',
                'allow_reply' => '',
                'allow_upload' => '',
                'allow_download' => '',
                'created_time' => 0,
                'created_username' => '',
                'created_userid' => 0,
                'created_ip' => 0,
                'style' => '',
            ],
        ]);
    }
}
