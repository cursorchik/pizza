<?php
// app/Services/Category/CategoryService.php

namespace App\Services\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Str;

class CategoryService
{
    public function getAll() : Collection { return Category::all(); }

    public function getBySlug(string $slug) : ?Category { return Category::where('slug', $slug)->first(); }

    public function getById(int $id) : Category { return Category::findOrFail($id); }

    public function create(array $data) : Category
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? Str::slug($data['name']),
        ]);
    }

    public function update(Category $category, array $data) : Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->name,
            'slug' => $data['slug'] ?? $category->slug,
        ]);
        return $category;
    }

    public function delete(Category $category) : void { $category->delete(); }
}
