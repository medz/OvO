<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwCommonConfig;

class PwCommonConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_common_config.php');
        foreach ($rows as $item) {
            PwCommonConfig::create([
                'name' => $item[0],
                'namespace' => $item[1],
                'value' => str_replace('\n', PHP_EOL, $item[2]),
                'vtype' => $item[3],
                'description' => $item[4],
            ]);
        }
    }
}
