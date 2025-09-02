<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $category = [
            [
                'name' => 'Item Category A',
                'company_id' => 1,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
           [
                'name' => 'Item Category B',
                'company_id' => 1,
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

         DB::table('item_categories')->insert($category);
    }
}
