<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Branch A',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sit amet bibendum nulla.',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Branch B',
                'description' => 'Lorem ipsum dolor elit. Mauris sit amet bibendum nulla.',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('branches')->insert($branches);
    }
}
