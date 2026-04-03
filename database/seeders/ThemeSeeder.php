<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        Theme::firstOrCreate(['slug' => 'dark-esports'], [
            'name' => 'Dark Esports',
            'color_primary' => '#0a0a0a',
            'color_secondary' => '#1a1a1a',
            'color_accent' => '#6366f1',
            'font_family' => 'Inter',
        ]);

        Theme::firstOrCreate(['slug' => 'light-cases'], [
            'name' => 'Light Cases',
            'color_primary' => '#f5f5f5',
            'color_secondary' => '#ffffff',
            'color_accent' => '#0066cc',
            'font_family' => 'Inter',
        ]);
    }
}
