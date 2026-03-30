<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderHistory;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
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

    public function test_user_can_create_order_with_cart()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $pizza = $this->getPizza();
        $drink = $this->getDrink();

        $this->withSession(['cart' => [
            "product_{$pizza->id}" => 2,
            "product_{$drink->id}" => 1,
        ]]);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(route('orders.store'), ['address' => 'Test Address 123']);

        $response->assertCreated();
        $orderId = $response->json('data.id');

        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'address' => 'Test Address 123']);
        $this->assertDatabaseHas('order_product', ['order_id' => $orderId, 'product_id' => $pizza->id, 'count' => 2]);
        $this->assertDatabaseHas('order_product', ['order_id' => $orderId, 'product_id' => $drink->id, 'count' => 1]);
        $this->assertNull(session('cart'));
    }

    public function test_user_cannot_create_order_with_empty_cart()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson(route('orders.store'), ['address' => 'Test Address 123']);

        $response->assertStatus(400)
            ->assertJsonPath('message', 'Cart is empty');
    }

    public function test_user_can_view_their_own_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('orders.show', $order));

        $response->assertOk()
            ->assertJsonPath('data.id', $order->id);
    }

    public function test_user_cannot_view_another_user_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('orders.show', $order));

        $response->assertForbidden();
    }

    public function test_admin_can_view_any_order()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('orders.show', $order));

        $response->assertOk();
    }

    public function test_admin_can_update_order_status_and_create_history()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['status' => 'preparing']);
        $pizza = $this->getPizza();
        $drink = $this->getDrink();
        $order->products()->attach($pizza->id, ['count' => 2]);
        $order->products()->attach($drink->id, ['count' => 1]);

        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson(route('orders.update', $order), ['status' => 'delivered']);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => 'delivered',
            'total_price' => ($pizza->price * 2) + ($drink->price * 1),
        ]);
    }

    public function test_non_admin_cannot_update_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson(route('orders.update', $order), ['status' => 'delivered']);

        $response->assertForbidden();
    }

    public function test_user_can_view_their_orders_list()
    {
        $user = User::factory()->create();
        Order::factory(3)->create(['user_id' => $user->id]);
        Order::factory(2)->create(); // чужие заказы
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('user.orders'));

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_can_view_history()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderHistory::factory()->create(['order_id' => $order->id, 'user_id' => $user->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson(route('orders.history'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
