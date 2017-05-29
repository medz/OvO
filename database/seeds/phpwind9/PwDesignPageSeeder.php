<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwDesignPage;

class PwDesignPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_design_page.php');
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
