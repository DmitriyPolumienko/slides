<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_home_route_redirects_to_presentations(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('presentations.index'));
    }
}
