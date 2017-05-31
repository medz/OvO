<?php

use Illuminate\Database\Seeder;

class PwCommonNavSeeder extends Seeder
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
            [1, 0, 1, 'main', 'default|index|run|', '首页', '', 'index.php', '', 0, 0, 1],
            [2, 0, 2, 'main', 'bbs|index|run|', '论坛', '|||', 'index.php?m=bbs', '', 0, 1, 2],
            [3, 0, 3, 'main', 'bbs|forumlist|run|', '版块', '', 'index.php?m=bbs&c=forumlist', '', 0, 1, 3],
            [4, 0, 4, 'main', 'like|like|run|', '喜欢', '|||', 'index.php?m=like&c=like', '', 0, 1, 4],
            [5, 0, 5, 'main', '', 'phpwind Fans', '|||', 'https://github.com/medz/phpwind/releases', '', 1, 1, 7],
            [6, 0, 6, 'main', 'tag|index|run|', '话题', '|||', 'index.php?m=tag', '', 0, 1, 5],
            [7, 0, 7, 'main', 'appcenter|index|run|', '应用', '', 'index.php?m=appcenter', '', 0, 1, 6],
            [8, 0, 8, 'my', 'space', '我的空间', '', 'index.php?m=space', '', 0, 1, 1],
            [9, 0, 9, 'my', 'fresh', '我的关注', '', 'index.php?m=my&c=fresh', '', 0, 1, 2],
            [10, 0, 10, 'my', 'forum', '我的版块', '', 'index.php?m=bbs&c=forum&a=my', '', 0, 1, 3],
            [11, 0, 11, 'my', 'article', '我的帖子', '', 'index.php?m=my&c=article', '', 0, 1, 4],
            [12, 0, 12, 'my', 'vote', '我的投票', '', 'index.php?m=vote&c=my', '', 0, 1, 5],
            [13, 0, 13, 'my', 'task', '我的任务', '', 'index.php?m=task', '', 0, 1, 6],
            [14, 0, 14, 'my', 'medal', '我的勋章', '', 'index.php?m=medal', '', 0, 1, 7],
            [15, 0, 15, 'bottom', '', 'phpwind fans', '', 'https://github.com/medz/phpwind', '', 0, 1, 1],
            [16, 0, 16, 'bottom', '', '联系我们', '|||', 'http://phpwind.com/contact.html', '', 0, 1, 2],
            [17, 0, 17, 'bottom', '', '程序建议', '', 'http://www.phpwind.net/thread/155', '', 0, 1, 3],
            [18, 0, 18, 'bottom', '', '问题反馈', '', 'http://www.phpwind.net/thread/155', '', 0, 1, 4],
        ];

        foreach ($rows as $item) {
            $this->container->make('db')->table('pw_common_nav')->insert([
                'navid' => $item[0],
                'parentid' => $item[1],
                'rootid' => $item[2],
                'type' => $item[3],
                'sign' => $item[4],
                'name' => $item[5],
                'style' => $item[6],
                'link' => $item[7],
                'alt' => $item[8],
                'target' => $item[9],
                'isshow' => $item[10],
                'orderid' => $item[11],
            ]);
        }
    }
}
