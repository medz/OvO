<?php

use Illuminate\Database\Seeder;

class PwCommonEmotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_common_emotion')->insert([
            ['emotion_id' => 1, 'category_id' => 1, 'emotion_name' => '弹', 'emotion_folder' => 'taodoll', 'emotion_icon' => '01.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 2, 'category_id' => 1, 'emotion_name' => '抱抱', 'emotion_folder' => 'taodoll', 'emotion_icon' => '02.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 3, 'category_id' => 1, 'emotion_name' => '晕', 'emotion_folder' => 'taodoll', 'emotion_icon' => '03.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 4, 'category_id' => 1, 'emotion_name' => '美味', 'emotion_folder' => 'taodoll', 'emotion_icon' => '04.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 5, 'category_id' => 1, 'emotion_name' => '烦', 'emotion_folder' => 'taodoll', 'emotion_icon' => '05.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 6, 'category_id' => 1, 'emotion_name' => '擦口水', 'emotion_folder' => 'taodoll', 'emotion_icon' => '06.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 7, 'category_id' => 1, 'emotion_name' => '思考', 'emotion_folder' => 'taodoll', 'emotion_icon' => '07.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 8, 'category_id' => 1, 'emotion_name' => '心跳', 'emotion_folder' => 'taodoll', 'emotion_icon' => '08.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 9, 'category_id' => 1, 'emotion_name' => '汗', 'emotion_folder' => 'taodoll', 'emotion_icon' => '09.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 10, 'category_id' =>  1, 'emotion_name' => '呸', 'emotion_folder' => 'taodoll', 'emotion_icon' => '10.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 11, 'category_id' =>  1, 'emotion_name' => '吐舌头', 'emotion_folder' => 'taodoll', 'emotion_icon' => '11.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 12, 'category_id' =>  1, 'emotion_name' => '加油', 'emotion_folder' => 'taodoll', 'emotion_icon' => '12.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 13, 'category_id' =>  1, 'emotion_name' => '吐', 'emotion_folder' => 'taodoll', 'emotion_icon' => '13.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 14, 'category_id' =>  1, 'emotion_name' => '大哭', 'emotion_folder' => 'taodoll', 'emotion_icon' => '14.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 15, 'category_id' =>  1, 'emotion_name' => '亲', 'emotion_folder' => 'taodoll', 'emotion_icon' => '15.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 16, 'category_id' =>  1, 'emotion_name' => '委屈', 'emotion_folder' => 'taodoll', 'emotion_icon' => '16.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 17, 'category_id' =>  1, 'emotion_name' => '眼镜', 'emotion_folder' => 'taodoll', 'emotion_icon' => '17.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 18, 'category_id' =>  1, 'emotion_name' => '抠鼻子', 'emotion_folder' => 'taodoll', 'emotion_icon' => '18.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 19, 'category_id' =>  1, 'emotion_name' => '臭美', 'emotion_folder' => 'taodoll', 'emotion_icon' => '19.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 20, 'category_id' =>  1, 'emotion_name' => '无奈', 'emotion_folder' => 'taodoll', 'emotion_icon' => '20.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 21, 'category_id' =>  1, 'emotion_name' => '槌子', 'emotion_folder' => 'taodoll', 'emotion_icon' => '21.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 22, 'category_id' =>  1, 'emotion_name' => '哇', 'emotion_folder' => 'taodoll', 'emotion_icon' => '22.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 23, 'category_id' =>  1, 'emotion_name' => '抱一抱', 'emotion_folder' => 'taodoll', 'emotion_icon' => '23.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 24, 'category_id' =>  1, 'emotion_name' => '不爽', 'emotion_folder' => 'taodoll', 'emotion_icon' => '24.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 25, 'category_id' =>  1, 'emotion_name' => '鼻血', 'emotion_folder' => 'taodoll', 'emotion_icon' => '25.gif', 'vieworder' => 0, 'isused' => 1],
            ['emotion_id' => 26, 'category_id' =>  1, 'emotion_name' => '帅', 'emotion_folder' => 'taodoll', 'emotion_icon' => '26.gif', 'vieworder' => 0, 'isused' => 1],
        ]);
    }
}
