<?php

namespace Tests\Feature;

use App\Models\Presentation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_presentations(): void
    {
        $response = $this->get(route('presentations.index'));
        $response->assertStatus(200);
    }

    public function test_can_view_create_form(): void
    {
        $response = $this->get(route('presentations.create'));
        $response->assertStatus(200);
    }

    public function test_can_create_presentation(): void
    {
        $response = $this->post(route('presentations.store'), [
            'title' => 'Test Presentation',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('presentations', ['title' => 'Test Presentation']);
    }

    public function test_can_view_builder(): void
    {
        $presentation = Presentation::factory()->create(['title' => 'Builder Test']);
        $response = $this->get(route('presentations.builder', $presentation));
        $response->assertStatus(200);
    }
}
