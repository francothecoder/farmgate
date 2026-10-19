<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('aria-label="Featured FarmGate stories"', false)
            ->assertSee('Maize grain for sale')
            ->assertSee('Vegetable seedling bundle')
            ->assertSee('marketplace/maize-grain-for-sale');
    }
}
