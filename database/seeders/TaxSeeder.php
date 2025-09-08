<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultEffectiveDate = '2024-01-01';

        $taxes = [
            [
                'name' => 'Sales Tax',
                'rate' => 8.25,
                'type' => 'sales',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'VAT',
                'rate' => 20.00,
                'type' => 'vat',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Service Tax',
                'rate' => 15.00,
                'type' => 'service',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Luxury Tax',
                'rate' => 25.00,
                'type' => 'luxury',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Import Duty',
                'rate' => 12.50,
                'type' => 'import',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Excise Tax',
                'rate' => 18.75,
                'type' => 'excise',
                'effective_date' => $defaultEffectiveDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Old Sales Tax (Inactive)',
                'rate' => 7.50,
                'type' => 'sales',
                'effective_date' => '2023-01-01',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('taxes')->insert($taxes);
    }
}
