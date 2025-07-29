<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
  public function run(): void
  {
    Language::create(['name' => "english"]);
    Language::create(['name' => "tagalog"]);
  }
}
