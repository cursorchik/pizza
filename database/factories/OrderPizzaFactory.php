<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderPizza;
use App\Models\Pizza;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderPizza>
 */
class OrderPizzaFactory extends Factory
{
    protected $model = OrderPizza::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'pizza_id' => Pizza::factory(),
            'count'    => $this->faker->numberBetween(1, 5),
        ];
    }
}
