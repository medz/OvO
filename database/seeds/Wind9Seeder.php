<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwCommonConfig;

class Wind9Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedCommonConfigTable();
    }

    /**
     * Seed to common_config.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function seedCommonConfigTable()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/phpwind9/pw_common_config.php');
        foreach ($rows as $item) {
            PwCommonConfig::create([
                'name' => $item[0],
                'namespace' => $item[1],
                'value' => $item[2],
                'vtype' => $item[3],
                'description' => $item[4],
            ]);
        }
    }
}
