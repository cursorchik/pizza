<?php
namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderHistoryFactory extends Factory
{
    protected $model = OrderHistory::class;

    public function definition() : array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['delivered', 'cancelled']),
            'address' => $this->faker->address,
            'delivery_time' => $this->faker->dateTimeBetween('-1 week'),
            'items' => json_encode([]),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
