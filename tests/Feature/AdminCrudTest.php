<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();
        Category::create(['name' => 'Пицца', 'slug' => 'pizza']);
        Category::create(['name' => 'Напитки', 'slug' => 'drinks']);
    }

    private function getPizzaCategoryId()
    {
        return Category::where('slug', 'pizza')->first()->id;
    }

    public function test_admin_can_create_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = auth('api')->login($admin);
        $categoryId = $this->getPizzaCategoryId();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(route('admin.products.store'), [
                'category_id' => $categoryId,
                'name' => 'Margherita',
                'price' => 9.99,
                'attributes' => [
                    ['name' => 'diameter', 'value' => '30 cm'],
                    ['name' => 'weight', 'value' => '400 g'],
                ],
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', ['name' => 'Margherita', 'price' => 9.99]);
        $this->assertDatabaseHas('product_attributes', [
            'attribute_name' => 'diameter',
            'attribute_value' => '30 cm',
        ]);
    }

    public function test_admin_can_update_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->putJson(route('admin.products.update', $product), [
                'name' => 'Updated Name',
                'price' => 12.99,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 12.99,
        ]);
    }

    public function test_admin_can_delete_product()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson(route('admin.products.destroy', $product));

        $response->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_non_admin_cannot_create_product()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);
        $categoryId = $this->getPizzaCategoryId();

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(route('admin.products.store'), [
                'category_id' => $categoryId,
                'name' => 'New Pizza',
                'price' => 9.99,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('products', ['name' => 'New Pizza']);
    }

    public function test_non_admin_cannot_update_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->putJson(route('admin.products.update', $product), [
                'name' => 'Hacked Name',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('products', ['id' => $product->id, 'name' => 'Hacked Name']);
    }

    public function test_non_admin_cannot_delete_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson(route('admin.products.destroy', $product));

        $response->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}
