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
        $this->call(PwUserGroupSeeder::class);
        $this->call(PwUserPermissionGroupSeeder::class);
        $this->call(PwWindIDAreaSeeder::class);
        $this->call(PwWindIDConfigSeeder::class);
        $this->call(PwWindIDSchoolSeeder::class);
        $this->call(PwAdminRoleSeeder::class);
        $this->call(PwBbsInfoSeeder::class);
        $this->call(PwBbsForumSeeder::class);
        $this->call(PwBbsForumExtraSeeder::class);
        $this->call(PwBbsForumStatisticsSeeder::class);
        $this->call(PwCommonEmotionCategorySeeder::class);
        $this->call(PwCommonEmotionSeeder::class);
        $this->call(PwCommonNavSeeder::class);
        $this->call(PwMedalInfoSeeder::class);
        $this->call(PwTaskSeeder::class);
        $this->call(PwTaskGroupSeeder::class);
    }
}
