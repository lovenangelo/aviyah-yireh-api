<?php

namespace Database\Seeders;

use App\Models\TrainingMaterial;
use Illuminate\Database\Seeder;

class TrainingMaterialSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            TrainingMaterial::create([
                'user_id' => 1,
                'category_id' => 2,
                'language_id' => 1,
                'expiration_date' => '2025-12-31',
                'title' => 'Sample title '.$i,
                'description' => 'A beginner-friendly introduction to JavaScript programming.',
                'duration' => 420,
                'files' => ['video'.$i.'.mp4'],
                'thumbnail_path' => 'thumbnail.jpg',
                'is_visible' => true,
                'views' => 0,
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            TrainingMaterial::create([
                'user_id' => 1,
                'category_id' => 1,
                'language_id' => 1,
                'expiration_date' => '2025-12-31',
                'title' => 'Sample PDF '.$i,
                'description' => 'A beginner-friendly introduction in PDF to JavaScript programming.',
                'duration' => 0,
                'files' => ['sample-pdf'.$i.'.pdf', 'sample-pdf'.$i.'.pdf'],
                'is_visible' => true,
                'views' => 0,
            ]);
        }

        TrainingMaterial::create([
            'user_id' => 1,
            'category_id' => 3,
            'language_id' => 1,
            'expiration_date' => '2025-12-31',
            'title' => 'Sample Images ',
            'description' => 'A beginner-friendly introduction in PDF to JavaScript programming.',
            'duration' => 0,
            'files' => ['image1.jpg', 'image2.jpg'],
            'is_visible' => true,
            'views' => 0,
        ]);
    }
}
