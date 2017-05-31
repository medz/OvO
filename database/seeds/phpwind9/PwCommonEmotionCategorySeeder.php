<?php

use Illuminate\Database\Seeder;

class PwCommonEmotionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_common_emotion_category')->insert([
            'category_id' => 1,
            'category_name' => '淘公仔',
            'emotion_folder' => 'taodoll',
            'emotion_apps' => 'bbs|weibo|cms|face',
            'orderid' => 0,
            'isopen' => 1,
        ]);
    }
}
