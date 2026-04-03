<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::firstOrCreate(['slug' => 'sample-project'], [
            'name' => 'Sample Project',
            'description' => 'A sample project for demonstration purposes.',
        ]);
    }
}
