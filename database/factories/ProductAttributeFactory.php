<?php
namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition() : array
    {
        return [
            'product_id' => Product::factory(),
            'attribute_name' => $this->faker->randomElement(['diameter', 'weight', 'volume', 'sugar_free']),
            'attribute_value' => $this->faker->randomElement(['30 cm', '400 g', '0.5 L', 'yes', 'no']),
        ];
    }
}
