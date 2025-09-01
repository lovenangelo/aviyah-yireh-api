<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Aws\Api\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ServiceCategory::create([
            'name' => 'Company A',
            'description' => 'All plumbing related services',
            'company_id' => 1,
        ]);
    }
}
