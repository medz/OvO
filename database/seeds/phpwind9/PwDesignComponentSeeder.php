<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwDesignComponent;

class PwDesignComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_design_component.php');
        foreach ($rows as $item) {
            PwDesignComponent::create([
                'model_flag' => $item[0],
                'comp_name' => $item[1],
                'comp_tpl' => $item[2],
                'sys_id' => $item[3],
            ]);
        }
    }
}
