<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['preparing', 'delivering', 'delivered', 'cancelled'];

        return [
            'user_id'       => User::factory(),
            'address'       => $this->faker->address,
            'status'        => $this->faker->randomElement($statuses),
            'delivery_time' => $this->faker->dateTimeBetween('+1 hour', '+3 days'),
        ];
    }
}
