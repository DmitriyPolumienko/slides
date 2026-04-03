<?php

namespace Database\Seeders;

use App\Models\MasterTemplate;
use App\Models\TemplateVersion;
use Illuminate\Database\Seeder;

class MasterTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Template 1: Dark Esports
        $darkTemplate = MasterTemplate::firstOrCreate(
            ['slug' => 'dark-esports-v1'],
            [
                'name' => 'Dark Esports v1',
                'description' => 'Dark themed template for esports presentations',
                'is_active' => true,
            ]
        );

        if ($darkTemplate->versions()->count() === 0) {
            TemplateVersion::create([
                'master_template_id' => $darkTemplate->id,
                'version' => '1.0',
                'is_active' => true,
                'schema' => [
                    'id' => 'template_dark_esports_v1',
                    'name' => 'Dark Esports v1',
                    'background' => '#0a0a0a',
                    'slide_types' => ['title_slide', 'chart_insight', 'comparison', 'media_grid', 'text_bullets'],
                ],
                'locked_zones' => [
                    'header' => [
                        'height_px' => 80,
                        'elements' => ['logo', 'project_name', 'date'],
                        'background' => '#111111',
                        'text_color' => '#ffffff',
                    ],
                    'footer' => [
                        'height_px' => 60,
                        'elements' => ['page_number', 'confidentiality_label'],
                        'background' => '#111111',
                        'text_color' => '#888888',
                    ],
                ],
                'editable_slots' => [
                    'title' => ['type' => 'text', 'max_chars' => 80, 'font_size' => 48, 'required' => true],
                    'subtitle' => ['type' => 'text', 'max_chars' => 150, 'font_size' => 24, 'required' => false],
                    'body' => ['type' => 'text', 'max_chars' => 500, 'font_size' => 16, 'required' => false],
                    'insight' => ['type' => 'text', 'max_chars' => 220, 'font_size' => 18, 'required' => false],
                    'chart' => ['type' => 'chart', 'allowed_types' => ['pie', 'bar', 'line'], 'required' => false],
                    'footnote' => ['type' => 'text', 'max_chars' => 120, 'font_size' => 12, 'required' => false],
                ],
            ]);
        }

        // Template 2: Light Cases
        $lightTemplate = MasterTemplate::firstOrCreate(
            ['slug' => 'light-cases-v1'],
            [
                'name' => 'Light Cases v1',
                'description' => 'Light themed template for case study presentations',
                'is_active' => true,
            ]
        );

        if ($lightTemplate->versions()->count() === 0) {
            TemplateVersion::create([
                'master_template_id' => $lightTemplate->id,
                'version' => '1.0',
                'is_active' => true,
                'schema' => [
                    'id' => 'template_light_cases_v1',
                    'name' => 'Light Cases v1',
                    'background' => '#f5f5f5',
                    'slide_types' => ['title_slide', 'two_column', 'media_grid', 'quote', 'text_bullets'],
                ],
                'locked_zones' => [
                    'header' => [
                        'height_px' => 72,
                        'elements' => ['logo', 'project_name'],
                        'background' => '#ffffff',
                        'text_color' => '#111111',
                    ],
                    'footer' => [
                        'height_px' => 56,
                        'elements' => ['page_number', 'date'],
                        'background' => '#f0f0f0',
                        'text_color' => '#555555',
                    ],
                ],
                'editable_slots' => [
                    'title' => ['type' => 'text', 'max_chars' => 80, 'font_size' => 40, 'required' => true],
                    'left_column_title' => ['type' => 'text', 'max_chars' => 60, 'font_size' => 24, 'required' => false],
                    'right_column_title' => ['type' => 'text', 'max_chars' => 60, 'font_size' => 24, 'required' => false],
                    'left_column_body' => ['type' => 'text', 'max_chars' => 400, 'font_size' => 14, 'required' => false],
                    'right_column_body' => ['type' => 'text', 'max_chars' => 400, 'font_size' => 14, 'required' => false],
                    'media_placeholder' => ['type' => 'image', 'required' => false],
                ],
            ]);
        }
    }
}
