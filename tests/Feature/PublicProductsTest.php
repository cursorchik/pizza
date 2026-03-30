<?php
namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicProductsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::create(['name' => 'Пицца', 'slug' => 'pizza']);
        Category::create(['name' => 'Напитки', 'slug' => 'drinks']);
    }

    public function test_can_list_all_products()
    {
        Product::factory(3)->create();

        $response = $this->getJson(route('products.index'));
        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_pizzas_by_category()
    {
        $pizzaCat = Category::where('slug', 'pizza')->first();
        $drinksCat = Category::where('slug', 'drinks')->first();

        Product::factory(3)->create(['category_id' => $pizzaCat->id]);
        Product::factory(2)->create(['category_id' => $drinksCat->id]);

        $response = $this->getJson(route('products.index', ['category' => 'pizza']));
        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_list_drinks_by_category()
    {
        $pizzaCat = Category::where('slug', 'pizza')->first();
        $drinksCat = Category::where('slug', 'drinks')->first();

        Product::factory(3)->create(['category_id' => $pizzaCat->id]);
        Product::factory(2)->create(['category_id' => $drinksCat->id]);

        $response = $this->getJson(route('products.index', ['category' => 'drinks']));
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson(route('products.show', $product));
        $response->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_404_when_product_not_found()
    {
        $response = $this->getJson(route('products.show', 9999));
        $response->assertNotFound()
            ->assertJsonPath('status', 'error');
    }
}
