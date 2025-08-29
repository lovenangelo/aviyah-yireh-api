<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'pdf']);
        Category::create(['name' => 'video']);
        Category::create(['name' => 'image']);
        Category::create(['name' => 'audio']);
    }
}
