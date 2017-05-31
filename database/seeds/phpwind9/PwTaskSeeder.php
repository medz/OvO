<?php

use Illuminate\Database\Seeder;

class PwTaskSeeder extends Seeder
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
            [1, 0, 1, 1, 0, 1, 0, 4197024000, 0, '发布一个帖子', '去版块发布一个帖子', '', '8,9,10,11,12,13,14,3,4,5,15,16', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:10:"postThread";s:3:"fid";s:1:"2";s:3:"num";s:1:"1";s:3:"url";s:18:"bbs/post/run?fid=2";}'],
            [2, 0, 0, 0, 9, 1, 0, 4197024000, 0, '增加自己的3个粉丝', '增加自己的3个粉丝', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:4:{s:4:"type";s:6:"member";s:5:"child";s:4:"fans";s:3:"num";d:3;s:3:"url";s:11:"my/fans/run";}'],
            [3, 0, 0, 0, 5, 1, 0, 4197024000, 0, '回复二个帖子', '回复二个帖子', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:5:"reply";s:3:"tid";s:1:"1";s:3:"url";s:18:"bbs/read/run?tid=1";s:3:"num";s:1:"2";}'],
            [4, 0, 1, 0, 6, 1, 0, 4197024000, 0, '喜欢一个帖子', '去喜欢一个帖子', '', '-1', 'a:5:{s:4:"type";s:6:"credit";s:3:"key";s:12:"id-name-unit";s:5:"value";s:12:"2-威望-点";s:3:"num";s:2:"10";s:8:"descript";s:11:"10点威望";}', 'a:5:{s:4:"type";s:3:"bbs";s:5:"child";s:4:"like";s:3:"fid";s:1:"2";s:3:"num";s:1:"1";s:3:"url";s:20:"bbs/thread/run?fid=2";}'],
        ];

        foreach ($rows as $key => $item) {
            $rows[$key] = [
                'taskid' => $item[0],
                'pre_task' => $item[1],
                'is_auto' => $item[2],
                'is_display_all' => $item[3],
                'view_order' => $item[4],
                'is_open' => $item[5],
                'start_time' => $item[6],
                'end_time' => $item[7],
                'period' => $item[8],
                'title' => $item[9],
                'description' => $item[10],
                'icon' => $item[11],
                'user_groups' => $item[12],
                'reward' => $item[13],
                'conditions' => $item[14],
            ];
        }

        $this->container->make('db')->table('pw_task')->insert($rows);
    }
}
