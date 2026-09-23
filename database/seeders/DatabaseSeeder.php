<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WorkUnitSeeder::class,
            DistrictAndVillageSeeder::class,
            UserSeeder::class,
            DtsenPurposeSeeder::class,
            ServiceTypeSeeder::class,
            ClientCategorySeeder::class,
            ComplaintCategorySeeder::class,
            ReferralInstitutionSeeder::class,
            InformationPageSeeder::class,
            ServiceRequestSeeder::class,
            RehabilitationSeeder::class,
            ComplaintSeeder::class,
            ActivityLogSeeder::class,
        ]);
    }
}
