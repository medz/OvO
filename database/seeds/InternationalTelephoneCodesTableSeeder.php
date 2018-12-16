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
        InternationalTelephoneCode::firstOrCreate([
            'code' => '+86',
        ], [
            'name' => '中国',
        ]);
    }
}
