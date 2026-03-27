<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pizza;
use App\Models\Drink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_admin_can_create_pizza()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/admin/pizzas', [
                'name' => 'New Pizza',
                'price' => 9.99,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('pizzas', ['name' => 'New Pizza', 'price' => 9.99]);
    }

    #[Test]
    public function test_admin_can_update_pizza()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pizza = Pizza::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/admin/pizzas/{$pizza->id}", [
                'name' => 'Updated Name',
                'price' => 12.99,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('pizzas', ['id' => $pizza->id, 'name' => 'Updated Name', 'price' => 12.99]);
    }

    #[Test]
    public function test_admin_can_delete_pizza()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $pizza = Pizza::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("/api/admin/pizzas/{$pizza->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('pizzas', ['id' => $pizza->id]);
    }

    public function test_non_admin_cannot_create_pizza()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/admin/pizzas', [
                'name' => 'New Pizza',
                'price' => 9.99,
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_create_drink()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/admin/drinks', [
                'name' => 'Cola',
                'price' => 2.99,
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('drinks', ['name' => 'Cola', 'price' => 2.99]);
    }

    #[Test]
    public function admin_can_update_drink()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $drink = Drink::factory()->create(['name' => 'Fanta', 'price' => 1.99]);
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->putJson("/api/admin/drinks/{$drink->id}", [
                'name' => 'Sprite',
                'price' => 2.49,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('drinks', ['id' => $drink->id, 'name' => 'Sprite', 'price' => 2.49]);
    }

    #[Test]
    public function admin_can_delete_drink()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $drink = Drink::factory()->create();
        $token = auth('api')->login($admin);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->deleteJson("/api/admin/drinks/{$drink->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('drinks', ['id' => $drink->id]);
    }

    #[Test]
    public function non_admin_cannot_create_drink()
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/admin/drinks', [
                'name' => 'Cola',
                'price' => 2.99,
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('drinks', ['name' => 'Cola']);
    }
}
