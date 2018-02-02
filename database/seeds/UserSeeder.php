<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createFounder();
        $this->createOwner();
    }

    /**
     * Create founder.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function createFounder()
    {
        // Create founder.
        $founder = factory(App\Models\User::class)->create([
            'login' => 'founder',
        ]);

        // Create founder role.
        $role = factory(App\Models\Role::class)->create([
            'name' => 'founder',
            'display_name' => '创始人',
        ]);

        $founder->roles()->sync($role);
    }

    /**
     * Create owner.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function createOwner()
    {
        // Create owner.
        $owner = factory(App\Models\User::class)->make([
            'login' => 'owner',
            'pw_salt' => rand(1000, 9999),
        ]);
        $owner->pw_password = md5(md5('password').$owner->pw_salt);
        $owner->save();

        // Create founder role.
        $role = factory(App\Models\Role::class)->create([
            'name' => 'owner',
            'display_name' => '普通用户',
        ]);

        $owner->roles()->sync($role);
    }
}
