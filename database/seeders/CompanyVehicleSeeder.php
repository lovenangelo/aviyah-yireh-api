<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanyVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = [
            [
                'plate_number' => 'ZYM9008',
                'make_model' => 'Toyota',
                'year' => '2020',
                'mileage' => '20000',
                'color' => 'red',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'plate_number' => 'ZYM9009',
                'make_model' => 'Honda',
                'year' => '2021',
                'mileage' => '20002',
                'color' => 'red',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'plate_number' => 'ZYM9010',
                'make_model' => 'BYD',
                'year' => '2022',
                'mileage' => '20012',
                'color' => 'yellow',
                'company_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('company_vehicles')->insert($item);
    }
}
