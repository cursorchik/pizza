<?php

namespace Database\Factories;

use App\Models\Drink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Drink>
 */
class DrinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Кока-кола',
                'Пепси',
                'Фанта',
                'Спрайт',
                'Лимонад',
                'Морс',
                'Чай холодный',
                'Кофе',
                'Сок апельсиновый',
                'Сок яблочный',
                'Минеральная вода',
            ]),
            'price' => $this->faker->numberBetween(50, 350),
        ];
    }
}
