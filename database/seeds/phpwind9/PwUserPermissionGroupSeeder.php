<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwUserPermissionGroup;

class PwUserPermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_user_permission_groups.php');
        foreach ($rows as $item) {
            PwUserPermissionGroup::create([
                'gid' => $item[0],
                'rkey' => $item[1],
                'rtype' => $item[2],
                'rvalue' => $item[3],
                'vtype' => $item[4],
            ]);
        }
    }
}
