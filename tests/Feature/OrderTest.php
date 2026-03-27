<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pizza;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_order_with_cart()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $pizza = Pizza::factory()->create();
        $drink = Drink::factory()->create();

        $this->withSession(['cart' => [
            "pizza_{$pizza->id}" => 2,
            "drink_{$drink->id}" => 1,
        ]]);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/orders', ['address' => 'Test Address 123']);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'address' => 'Test Address 123']);
        $this->assertDatabaseHas('orders_pizzas', ['order_id' => $response->json('data.id'), 'pizza_id' => $pizza->id, 'count' => 2]);
        $this->assertDatabaseHas('orders_drinks', ['order_id' => $response->json('data.id'), 'drink_id' => $drink->id, 'count' => 1]);
        $this->assertNull(session('cart')); // корзина очищена
    }

    public function test_user_cannot_create_order_with_empty_cart()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/orders', ['address' => 'Test Address 123']);

        $response->assertBadRequest()
            ->assertJsonPath('message', 'Cart is empty!');
    }

    public function test_user_can_view_their_own_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson("/api/orders/{$order->id}");

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
            ->getJson("/api/orders/{$order->id}");

        $response->assertForbidden();
    }

    public function test_admin_can_view_any_order()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson("/api/orders/{$order->id}");

        $response->assertOk();
    }

    public function test_admin_can_update_order_status_and_create_history()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $order = Order::factory()->create(['status' => 'preparing']);
        // добавим позиции для проверки истории
        $pizza = Pizza::factory()->create(['price' => 100]);
        $order->pizzas()->attach($pizza->id, ['count' => 2]);
        $drink = Drink::factory()->create(['price' => 50]);
        $order->drinks()->attach($drink->id, ['count' => 1]);

        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/orders/{$order->id}", ['status' => 'delivered']);

        $response->assertOk();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => 'delivered',
            'total_price' => (100*2 + 50*1),
        ]);
    }

    public function test_non_admin_cannot_update_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/orders/{$order->id}", ['status' => 'delivered']);

        $response->assertForbidden();
    }

    public function test_user_can_view_their_orders_list()
    {
        $user = User::factory()->create();
        Order::factory(3)->create(['user_id' => $user->id]);
        Order::factory(2)->create(); // чужие заказы
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/user/orders');

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
            ->getJson('/api/orders/history');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
