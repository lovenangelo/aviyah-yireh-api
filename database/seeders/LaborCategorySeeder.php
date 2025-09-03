<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaborCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = [
            [
                'name' => 'Labor Category A',
                'cost' => 114.25,
                'selling_price' => 200.00,
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.",
                'company_id' => 1,
                'category_id'=> 2,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Labor Category B',
                'cost' => 121.25,
                'selling_price' => 201.00,
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.",
                'company_id' => 1,
                'category_id'=> 2,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
             [
                'name' => 'Labor Category C',
                'cost' => 132.25,
                'selling_price' => 202.00,
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.",
                'company_id' => 1,
                'category_id'=> 2,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('labor_categories')->insert($category);
    }
}
