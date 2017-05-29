<?php

use Illuminate\Database\Seeder;

class PwTaskGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function run()
    {
        $this->container->make('db')->table('pw_task_group')->insert([
            ['taskid' => 1, 'gid' => -1, 'is_auto' => 1, 'end_time' => '4197024000'],
            ['taskid' => 2, 'gid' => -1, 'is_auto' => 0, 'end_time' => '4197024000'],
            ['taskid' => 3, 'gid' => -1, 'is_auto' => 0, 'end_time' => '4197024000'],
            ['taskid' => 4, 'gid' => -1, 'is_auto' => 1, 'end_time' => '4197024000'],
        ]);
    }
}
