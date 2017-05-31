<?php

use Illuminate\Database\Seeder;

// use Illuminate\Support\Facades\DB;

class PwBbsInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->container->make('db')->table('pw_bbsinfo')->insert(
            ['id' => 1, 'newmember' => '', 'totalmember' => 0, 'higholnum' => 0, 'higholtime' => 0, 'yposts' => 0, 'hposts' => 0]
        );
    }
}
