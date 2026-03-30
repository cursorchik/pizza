<?php
namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition() : array
    {
        return [
            'user_id' => User::factory(),
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(['preparing', 'delivering', 'delivered', 'cancelled']),
            'delivery_time' => $this->faker->dateTimeBetween('+1 hour', '+3 days'),
        ];
    }
}
