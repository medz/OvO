<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwCommonConfig;
use Medz\Wind\Models\PwDesignComponent;
use Medz\Wind\Models\PwDesignPage;

class Wind9Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->seedCommonConfigTable();
        $this->seedDesignComponent();
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
                'value' => str_replace('\n', PHP_EOL, $item[2]),
                'vtype' => $item[3],
                'description' => $item[4],
            ]);
        }
    }

    /**
     * Seed to design_component.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function seedDesignComponent()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/phpwind9/pw_design_component.php');
        foreach ($rows as $item) {
            PwDesignComponent::create([
                'model_flag' => $item[0],
                'comp_name' => $item[1],
                'comp_tpl' => $item[2],
                'sys_id' => $item[3],
            ]);
        }

        $rows = $this->container->make('files')->getRequire(__DIR__.'/phpwind9/pw_design_page.php');
        foreach ($rows as $item) {
            PwDesignPage::create([
                'page_id' => $item[0],
                'page_type' => $item[1],
                'page_name' => $item[2],
                'page_router' => $item[3],
                'page_unique' => $item[4],
                'is_unique' => $item[5],
                'module_ids' => $item[6],
                'struct_names' => $item[7],
                'segments' => $item[8],
                'design_lock' => $item[9]
            ]);
        }
    }
}
