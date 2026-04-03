<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            ThemeSeeder::class,
            ProjectSeeder::class,
            MasterTemplateSeeder::class,
        ]);
    }
}
