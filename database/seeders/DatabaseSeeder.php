<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pizzaCat = Category::create(['name' => 'Пицца', 'slug' => 'pizza']);
        $drinksCat = Category::create(['name' => 'Напитки', 'slug' => 'drinks']);

        $pizza = Product::create([
            'name' => 'Маргарита',
            'price' => 450.00,
            'category_id' => $pizzaCat->id,
        ]);
        $pizza->attributes()->createMany([
            ['attribute_name' => 'diameter', 'attribute_value' => '30 cm'],
            ['attribute_name' => 'weight', 'attribute_value' => '400 g'],
        ]);

        $drink = Product::create([
            'name' => 'Кока-кола',
            'price' => 120.00,
            'category_id' => $drinksCat->id,
        ]);
        $drink->attributes()->createMany([
            ['attribute_name' => 'volume', 'attribute_value' => '0.5 L'],
            ['attribute_name' => 'sugar_free', 'attribute_value' => 'no'],
        ]);

        // Дополнительно можно создать случайные продукты
        // Product::factory(5)->create()->each(fn($p) => ProductAttribute::factory(2)->create(['product_id' => $p->id]));
    }
}
