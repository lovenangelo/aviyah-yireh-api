<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingMaterial;

class TrainingMaterialSeeder extends Seeder
{
  public function run(): void
  {
    for ($i = 1; $i <= 5; $i++) {
      TrainingMaterial::create([
        'user_id' => 1,
        'category_id' => 2,
        'language_id' => 1,
        'expiration_date' => '2025-12-31',
        'title' => 'Sample title ' . $i,
        'description' => 'A beginner-friendly introduction to JavaScript programming.',
        'duration' => 420,
        'path' => 'video1.mp4',
        'thumbnail_path' => 'thumbnail.jpg',
        'is_visible' => true,
        'views' => 0,
      ]);
    }
  }
}
