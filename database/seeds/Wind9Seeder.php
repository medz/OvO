<?php

use Illuminate\Database\Seeder;

class Wind9Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PwCommonConfigSeeder::class);
        $this->call(PwDesignComponentSeeder::class);
        $this->call(PwDesignPageSeeder::class);
    }
}
