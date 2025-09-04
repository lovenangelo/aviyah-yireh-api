<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $service_category = [
            [
                'name' => 'Service Category A',
                'company_id' => 1,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Service Category B',
                'company_id' => 1,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('service_categories')->insert($service_category);
    }
}
