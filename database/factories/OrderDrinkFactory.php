<?php

namespace Database\Factories;

use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderDrink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderDrink>
 */
class OrderDrinkFactory extends Factory
{
    protected $model = OrderDrink::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'drink_id' => Drink::factory(),
            'count'    => $this->faker->numberBetween(1, 5),
        ];
    }
}
