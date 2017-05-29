<?php

use Illuminate\Database\Seeder;

class PwBbsForumExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_bbs_forum_extra')->insert([
            [
                'fid' => 1,
                'seo_description' => '',
                'seo_keywords' => '',
                'settings_basic' => 'a:4:{s:7:"jumpurl";s:0:"";s:16:"numofthreadtitle";i:50;s:13:"threadperpage";i:20;s:11:"readperpage";i:15;}',
                'settings_credit' => 'a:0:{}',
            ],
            [
                'fid' => 2,
                'seo_description' => '',
                'seo_keywords' => '',
                'settings_basic' => 'a:26:{s:16:"numofthreadtitle";i:50;s:13:"threadperpage";i:20;s:11:"readperpage";i:15;s:18:"minlengthofcontent";i:3;s:8:"locktime";s:0:"";s:8:"edittime";s:0:"";s:12:"contentcheck";i:0;s:7:"ifthumb";i:0;s:10:"thumbwidth";s:0:"";s:11:"thumbheight";s:0:"";s:8:"anticopy";i:0;s:11:"copycontent";s:0:"";s:5:"water";i:0;s:8:"allowrob";i:0;s:9:"allowhide";i:1;s:9:"allowsell";i:1;s:11:"allowencode";i:0;s:9:"anonymous";i:0;s:9:"allowtype";a:1:{i:0;s:7:"default";}s:9:"typeorder";a:1:{s:7:"default";i:0;}s:7:"jumpurl";s:0:"";s:10:"topic_type";i:0;s:16:"force_topic_type";i:0;s:14:"thread_visible";i:0;s:8:"waterimg";s:8:"mark.gif";s:18:"topic_type_display";i:1;}',
                'settings_credit' => 'a:10:{s:10:"post_topic";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:12:"delete_topic";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:10:"post_reply";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:12:"delete_reply";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:12:"digest_topic";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:13:"remove_digest";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:11:"push_thread";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:10:"upload_att";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:12:"download_att";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}s:6:"belike";a:2:{s:5:"limit";s:0:"";s:6:"credit";a:3:{i:1;s:0:"";i:2;s:0:"";i:3;s:0:"";}}}',
            ]
        ]);
    }
}
