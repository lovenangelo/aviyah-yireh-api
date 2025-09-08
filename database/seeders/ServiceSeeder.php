<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Service A',
                'description' => 'Lorem ipsum dolor sit ametd, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'company_id' => 1,
                'service_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name' => 'Service B',
                'description' => 'Lorem ipsum dolor sit ametf, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'company_id' => 1,
                'service_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'name' => 'Service C',
                'description' => 'Lorem ipsum dolor sit damet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'company_id' => 1,
                'service_category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('services')->insert($services);
    }
}
