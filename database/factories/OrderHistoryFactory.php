<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderHistory>
 */
class OrderHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id'      => Order::factory(),
            'user_id'       => User::factory(),
            'status'        => 'delivered',
            'address'       => fake()->address(),
            'delivery_time' => now(),
            'items'         => json_encode([]),
            'total_price'   => fake()->randomFloat(2, 10, 100),
        ];
    }
}
