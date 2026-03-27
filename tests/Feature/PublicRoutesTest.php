<?php

namespace Tests\Feature;

use App\Models\Pizza;
use App\Models\Drink;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_pizzas()
    {
        Pizza::factory(3)->create();

        $response = $this->getJson('/api/pizzas');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_show_pizza()
    {
        $pizza = Pizza::factory()->create();

        $response = $this->getJson("/api/pizzas/{$pizza->id}");
        $response->assertOk()->assertJsonPath('data.id', $pizza->id);
    }

    public function test_404_when_pizza_not_found()
    {
        $response = $this->getJson('/api/pizzas/9999');
        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }

    // Тесты для напитков
    public function test_can_list_drinks()
    {
        Drink::factory(3)->create();

        $response = $this->getJson('/api/drinks');
        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_can_show_drink()
    {
        $drink = Drink::factory()->create();

        $response = $this->getJson("/api/drinks/{$drink->id}");
        $response->assertOk()->assertJsonPath('data.id', $drink->id);
    }

    public function test_404_when_drink_not_found()
    {
        $response = $this->getJson('/api/drinks/9999');
        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }
}
