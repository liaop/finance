<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiOnlyRoutingTest extends TestCase
{
    public function test_web_routes_are_disabled_for_api_only_app(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_register_route_is_available_without_api_prefix(): void
    {
        $response = $this->post('/register', []);

        $response->assertStatus(422);
    }
}
