<?php

use Carbon\Carbon;
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
        $codes = json_decode(file_get_contents(resource_path('international-telephone-code.json')), true);
        foreach ($codes as $code) {
            InternationalTelephoneCode::firstOrCreate([
                'code' => $code['code'],
                'name' => $code['name'],
            ], [
                'icon' => $code['icon'],
                'enabled_at' => new Carbon,
            ]);
        }
    }
}
