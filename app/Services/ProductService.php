<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getAll(?string $categorySlug = null) : Collection
    {
        $query = Product::with('category', 'attributes');

        if ($categorySlug)
        {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) $query->where('category_id', $category->id);
        }

        return $query->get();
    }

    public function getById(int $id) : Product
    {
        return Product::with('category', 'attributes')->findOrFail($id);
    }

    /**
     * @throws Exception
     */
    public function create(array $data) : Product
    {
        $productWithSameName = Product::where('name', $data['name'])->get('id');
        if ($productWithSameName->count() > 0) throw new Exception('Product with this name already exists', 409);

        $product = Product::create([
            'name'        => $data['name'],
            'price'       => $data['price'],
            'category_id' => $data['category_id'],
        ]);

        if (isset($data['attributes']))
        {
            foreach ($data['attributes'] as $attr)
            {
                $product->attributes()->create([
                    'attribute_name'  => $attr['name'],
                    'attribute_value' => $attr['value'],
                ]);
            }
        }

        return $product->load('attributes');
    }

    /**
     * @throws Exception
     */
    public function update(Product $product, array $data) : Product
    {
        $productWithSameName = Product::where('name', $data['name'])->get('id');
        if ($productWithSameName->count() > 0) throw new Exception('Product with this name already exists', 409);

        $product->update([
            'name'        => $data['name'] ?? $product->name,
            'price'       => $data['price'] ?? $product->price,
            'category_id' => $data['category_id'] ?? $product->category_id,
        ]);

        if (isset($data['attributes']))
        {
            $product->attributes()->delete();
            foreach ($data['attributes'] as $attr)
            {
                $product->attributes()->create([
                    'attribute_name'  => $attr['name'],
                    'attribute_value' => $attr['value'],
                ]);
            }
        }

        return $product->load('attributes');
    }

    public function delete(Product $product) : void
    {
        $product->delete();
    }
}
