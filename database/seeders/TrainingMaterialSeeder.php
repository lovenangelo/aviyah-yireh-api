<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingMaterial;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrainingMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Seed Categories
        $categories = [
            'Technology',
            'Business',
            'Education',
            'Entertainment',
            'Health & Wellness',
            'Sports',
            'Travel',
            'Food & Cooking',
            'Fashion',
            'Art & Design',
            'Music',
            'Photography',
            'Science',
            'Politics',
            'Environment',
            'Automotive',
            'Real Estate',
            'Finance',
            'Gaming',
            'Books & Literature'
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Seed Languages
        $languages = [
            'English',
            'Spanish',
            'French',
            'German',
            'Italian',
            'Portuguese',
            'Russian',
            'Chinese (Simplified)',
            'Chinese (Traditional)',
            'Japanese',
            'Korean',
            'Arabic',
            'Hindi',
            'Dutch',
            'Swedish',
            'Norwegian',
            'Danish',
            'Finnish',
            'Polish',
            'Czech',
            'Hungarian',
            'Romanian',
            'Bulgarian',
            'Greek',
            'Turkish',
            'Hebrew',
            'Thai',
            'Vietnamese',
            'Indonesian',
            'Malay'
        ];

        foreach ($languages as $language) {
            DB::table('languages')->insert([
                'name' => $language,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

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
                'file_type' => 'video',
                'thumbnail_path' => 'thumbnail.jpg',
                'is_visible' => true,
                'views' => 0,
            ]);
        }
    }
}
