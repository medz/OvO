<?php

use Illuminate\Database\Seeder;
use Medz\Wind\Models\PwWindIDSchool;

class PwWindIDSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = $this->container->make('files')->getRequire(__DIR__.'/data/pw_windid_school.php');
        $output = $this->command->getOutput();
        $output->progressStart(count($rows));

        foreach ($rows as $item) {
            PwWindIDSchool::create([
                'schoolid' => $item[0],
                'name' => $item[1],
                'areaid' => $item[2],
                'typeid' => $item[3],
                'first_char' => $item[4],
            ]);

            $output->progressAdvance(1);
        }

        $output->progressFinish();
    }
}
