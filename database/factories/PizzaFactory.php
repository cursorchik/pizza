<?php

namespace Database\Factories;

use App\Models\Pizza;
use Faker\Core\Number;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pizza>
 */
class PizzaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                    'Пицца',
                    'Пицца с',
                    'Острая пицца с',
                    'Домашняя пицца с',
                ]) . ' ' . $this->faker->randomElement([
                    'пепперони',
                    'ветчиной',
                    'грибами',
                    'овощами',
                    'сыром',
                    'морепродуктами',
                    'беконом',
                    'курицей',
                ]),
            'price' => $this->faker->numberBetween(300, 1200)
        ];
    }
}
