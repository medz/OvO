<?php

use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a default forum.
        $forum = factory(App\Models\Forum::class)->create([
            'name' => 'Fans',
        ]);

        // Create a default category.
        $category = factory(App\Models\ForumTopicCategory::class)->make([
            'name' => '感谢安装',
        ]);
        $forum->categories()->save($category);

        // Query a first user
        $user = App\Models\User::first();

        // Create a topic.
        $topic = factory(App\Models\ForumTopic::class)->make([
            'forum_topic_categories_id' => $category->id,
            'user_id' => $user->id,
            'subject' => '👏感谢安装 Fans 2 社区程序❤️',
            'body' => <<<'RAW'
## 🙏感谢安装

感谢你安装 [Fans](https://github.com/medz/phpwind)
RAW
        ]);
        $forum->topics()->save($topic);
        $forum->topic_count++;
        $forum->save();
        $category->topic_count++;
        $category->save();
    }
}
