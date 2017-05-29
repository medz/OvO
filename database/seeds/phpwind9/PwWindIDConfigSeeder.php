<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwWindIDConfig;

class PwWindIDConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_windid_config.php');
        foreach ($rows as $item) {
            PwWindIDConfig::create([
                'name' => $item[0],
                'namespace' => $item[1],
                'value' => $item[2],
                'vtype' => $item[3],
                'descrip' => $item[4],
            ]);
        }
    }
}
