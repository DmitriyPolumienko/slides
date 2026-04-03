<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::firstOrCreate(['code' => 'en'], ['name' => 'English']);
        Language::firstOrCreate(['code' => 'ru'], ['name' => 'Russian']);
    }
}
