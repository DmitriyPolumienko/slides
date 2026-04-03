<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PresentationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'status' => 'draft',
            'header_options' => ['show_logo' => true, 'show_project_name' => true, 'show_date' => false],
            'footer_options' => ['show_page_number' => true, 'show_confidentiality' => false],
        ];
    }
}
