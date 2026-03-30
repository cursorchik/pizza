<?php
namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition() : array
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->words(2, true),
            'price' => $this->faker->randomFloat(2, 100, 1500),
        ];
    }

    public function forCategory(string $slug) : ProductFactory
    {
        return $this->state(function (array $attributes) use ($slug)
        {
            $category = Category::where('slug', $slug)->first();
            if (!$category) $category = Category::factory()->create(['slug' => $slug, 'name' => ucfirst($slug)]);
            return ['category_id' => $category->id];
        });
    }
}

























