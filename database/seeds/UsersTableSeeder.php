<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create founder.
        $founder = factory(App\Models\User::class)->create([
            'login' => 'founder',
        ]);
        $founder->roles()->sync(1);

        // Create owner.
        $owner = factory(App\Models\User::class)->make([
            'login' => 'owner',
            'pw_salt' => rand(1000, 9999),
        ]);
        $owner->pw_password = md5(md5('password').$owner->pw_salt);
        $owner->save();
        $owner->roles()->sync(2);
    }
}
