<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Role::class)->make([
            'name' => 'founder',
            'display_name' => '创始人',
        ]);

        factory(App\Models\Role::class)->make([
            'name' => 'owner',
            'display_name' => '普通用户',
        ]);
    }
}
