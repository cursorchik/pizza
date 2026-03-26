<?php

namespace Database\Seeders;

use App\Models\Pizza;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderPizza;
use App\Models\OrderDrink;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Pizza::factory(10)->create();
        Drink::factory(10)->create();

        // Создаём 20 заказов, для каждого добавим 1-3 пиццы и 0-2 напитка
        Order::factory(20)->afterCreating(function (Order $order)
        {
            // Добавляем пиццы
            $pizzas = Pizza::inRandomOrder()->take(rand(1, 3))->get();
            foreach ($pizzas as $pizza)
            {
                OrderPizza::factory()->create([
                    'order_id' => $order->id,
                    'pizza_id' => $pizza->id,
                    'count'    => rand(1, 3),
                ]);
            }

            // Добавляем напитки
            $drinks = Drink::inRandomOrder()->take(rand(0, 2))->get();
            foreach ($drinks as $drink)
            {
                OrderDrink::factory()->create([
                    'order_id' => $order->id,
                    'drink_id' => $drink->id,
                    'count'    => rand(1, 2),
                ]);
            }
        })
        ->create();
    }
}
