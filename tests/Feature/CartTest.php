<?php

namespace Tests\Feature;

use App\Models\Pizza;
use App\Models\Drink;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_pizza_to_cart()
    {
        $pizza = Pizza::factory()->create();

        $response = $this->postJson('/api/cart/add', [
            'type'     => 'pizza',
            'id'       => $pizza->id,
            'quantity' => 2,
        ]);

        $response->assertOk();
        $this->assertEquals(2, session('cart')["pizza_{$pizza->id}"]);
    }

    public function test_can_update_cart_item()
    {
        $pizza = Pizza::factory()->create();
        $this->withSession(['cart' => ["pizza_{$pizza->id}" => 1]]);

        $response = $this->patchJson("/api/cart/pizza/{$pizza->id}", ['quantity' => 5]);

        $response->assertOk();
        $this->assertEquals(5, session('cart')["pizza_{$pizza->id}"]);
    }

    public function test_can_remove_cart_item()
    {
        $pizza = Pizza::factory()->create();
        $this->withSession(['cart' => ["pizza_{$pizza->id}" => 1]]);

        $response = $this->deleteJson("/api/cart/pizza/{$pizza->id}");

        $response->assertOk();
        $this->assertArrayNotHasKey("pizza_{$pizza->id}", session('cart', []));
    }

    public function test_can_view_cart()
    {
        $pizza = Pizza::factory()->create(['price' => 10]);
        $drink = Drink::factory()->create(['price' => 5]);

        $this->withSession(['cart' => [
            "pizza_{$pizza->id}" => 2,
            "drink_{$drink->id}" => 3,
        ]]);

        $response = $this->getJson('/api/cart');
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
