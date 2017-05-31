<?php

use Illuminate\Database\Seeder;

class PwBbsForumStatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_bbs_forum_statistics')->insert([
            ['fid' => 1, 'todayposts' => 0, 'todaythreads' => 0, 'article' => 0, 'posts' => 0, 'threads' => 0, 'subposts' => 0, 'subthreads' => 0, 'lastpost_info' => '', 'lastpost_time' => 0, 'lastpost_username' => '', 'lastpost_tid' =>0],
            ['fid' => 2, 'todayposts' => 0, 'todaythreads' => 0, 'article' => 0, 'posts' => 0, 'threads' => 0, 'subposts' => 0, 'subthreads' => 0, 'lastpost_info' => '', 'lastpost_time' => 0, 'lastpost_username' => '', 'lastpost_tid' =>0],
        ]);
    }
}
