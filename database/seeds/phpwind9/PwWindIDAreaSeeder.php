<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwWindIDArea;

class PwWindIDAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_windid_area.php');
        $output = $this->command->getOutput();
        $output->progressStart(count($rows));

        foreach ($rows as $item) {
            PwWindIDArea::create([
                'areaid' => $item[0],
                'name' => $item[1],
                'joinname' => $item[2],
                'parentid' => $item[3],
                'vieworder' => $item[4],
            ]);

            $output->progressAdvance(1);
        }

        $output->progressFinish();
    }
}
