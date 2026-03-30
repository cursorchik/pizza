<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();
        Category::create(['name' => 'Пицца', 'slug' => 'pizza']);
        Category::create(['name' => 'Напитки', 'slug' => 'drinks']);
    }

    private function getPizza() : Model|Collection
    {
        return Product::factory()->forCategory('pizza')->create();
    }

    private function getDrink() : Model|Collection
    {
        return Product::factory()->forCategory('drinks')->create();
    }

    public function test_can_add_item_to_cart()
    {
        $pizza = $this->getPizza();

        $response = $this->postJson(route('cart.add'), [
            'id' => $pizza->id,
            'quantity' => 2,
        ]);

        $response->assertOk();
        $this->assertEquals(2, session('cart')["product_{$pizza->id}"]);
    }

    public function test_can_update_cart_item()
    {
        $pizza = $this->getPizza();
        $this->withSession(['cart' => ["product_{$pizza->id}" => 1]]);

        $response = $this->patchJson(route('cart.update', $pizza->id), ['quantity' => 5]);

        $response->assertOk();
        $this->assertEquals(5, session('cart')["product_{$pizza->id}"]);
    }

    public function test_can_remove_cart_item()
    {
        $pizza = $this->getPizza();
        $this->withSession(['cart' => ["product_{$pizza->id}" => 1]]);

        $response = $this->deleteJson(route('cart.destroy', $pizza->id));

        $response->assertOk();
        $this->assertArrayNotHasKey("product_{$pizza->id}", session('cart', []));
    }

    public function test_can_view_cart()
    {
        $pizza = $this->getPizza();
        $drink = $this->getDrink();

        $this->withSession(['cart' => [
            "product_{$pizza->id}" => 2,
            "product_{$drink->id}" => 3,
        ]]);

        $response = $this->getJson(route('cart.index'));
        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }

    public function test_cannot_exceed_max_pizzas()
    {
        $pizza = $this->getPizza();

        // Уже есть 9 пицц в корзине
        $cart = [];
        for ($i = 1; $i <= 9; $i++) $cart["product_{$i}"] = 1;

        $this->withSession(['cart' => $cart]);

        $response = $this->postJson(route('cart.add'), [
            'id' => $pizza->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Maximum 10 pizzas allowed in cart');
    }

    public function test_cannot_exceed_max_drinks()
    {
        $drink = $this->getDrink();

        // Уже есть 19 напитков
        $cart = [];
        for ($i = 1; $i <= 19; $i++) $cart["product_{$i}"] = 1;

        $this->withSession(['cart' => $cart]);

        $response = $this->postJson(route('cart.add'), [
            'id' => $drink->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Maximum 20 drinks allowed in cart');
    }
}
