<?php

use Illuminate\Database\Seeder;
use App\Models\InternationalTelephoneCode;

class InternationalTelephoneCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach([
            '+1'  => 'United States',
            '+1'  => 'Canada',
            '+44' => 'United Kingdom',
            '+49' => 'Deutschland',
            '+81' => 'にっぽんこく',
            '+82' => '대한민국',
            '+86' => '中国',
        ] as $code => $name) {
            InternationalTelephoneCode::firstOrCreate([
                'code' => $code,
            ], [
                'name' => $name,
            ]);
        }
    }
}
