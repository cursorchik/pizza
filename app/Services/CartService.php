<?php

namespace App\Services;

use App\Models\Product;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCart(): array
    {
        return Session::get('cart', []);
    }

    /**
     * @throws Exception
     */
    public function addItem(int $productId, int $quantity = 1) : array
    {
        $product = Product::with('category')->findOrFail($productId);
        $cart = $this->getCart();
        $key = "product_{$productId}";

        // Проверка лимитов
        $totalCurrent = array_sum($cart);
        $newTotal = $totalCurrent + $quantity;
        $categorySlug = $product->category->slug;

        if ($categorySlug === 'pizza' && $newTotal > 10) throw new Exception('Maximum 10 pizzas allowed in cart');
        if ($categorySlug === 'drinks' && $newTotal > 20) throw new Exception('Maximum 20 drinks allowed in cart');

        $cart[$key] = ($cart[$key] ?? 0) + $quantity;
        Session::put('cart', $cart);

        return $this->formatCart($cart);
    }

    /**
     * @throws Exception
     */
    public function updateItem(int $productId, int $quantity) : array
    {
        $cart = $this->getCart();
        $key = "product_{$productId}";

        if (!isset($cart[$key])) throw new Exception('Item not found in cart');

        $product = Product::with('category')->findOrFail($productId);
        $otherSum = array_sum(array_diff_key($cart, [$key => 0]));
        $newTotal = $otherSum + $quantity;
        $categorySlug = $product->category->slug;

        if ($categorySlug === 'pizza' && $newTotal > 10) throw new Exception('Maximum 10 pizzas allowed in cart');
        if ($categorySlug === 'drinks' && $newTotal > 20) throw new Exception('Maximum 20 drinks allowed in cart');

        if ($quantity <= 0) unset($cart[$key]);
        else $cart[$key] = $quantity;

        Session::put('cart', $cart);
        return $this->formatCart($cart);
    }

    /**
     * @throws Exception
     */
    public function removeItem(int $productId) : array
    {
        $cart = $this->getCart();
        $key = "product_{$productId}";

        if (!isset($cart[$key])) throw new Exception('Item not found in cart');

        unset($cart[$key]);
        Session::put('cart', $cart);
        return $this->formatCart($cart);
    }

    public function clearCart() : void { Session::forget('cart'); }

    public function getCartProducts() : Collection
    {
        $cart = $this->getCart();
        if (empty($cart)) return collect();

        $productIds = [];
        $quantities = [];
        foreach ($cart as $key => $quantity)
        {
            $productId = (int) str_replace('product_', '', $key);
            $productIds[] = $productId;
            $quantities[$productId] = $quantity;
        }

        $products = Product::whereIn('id', $productIds)
            ->with('category', 'attributes')
            ->get()
            ->keyBy('id');

        foreach ($products as $product) $product->quantity = $quantities[$product->id];

        return $products;
    }

    public function formatCart(array $cart): array
    {
        $items = [];
        foreach ($cart as $key => $quantity)
        {
            $productId = (int) str_replace('product_', '', $key);
            $product = Product::with('category', 'attributes')->find($productId);
            if ($product)
            {
                $items[] = [
                    'id'         => $product->id,
                    'type'       => $product->category->slug,
                    'name'       => $product->name,
                    'price'      => (float) $product->price,
                    'quantity'   => $quantity,
                    'total'      => $product->price * $quantity,
                    'attributes' => $product->attributes,
                ];
            }
        }
        return $items;
    }
}
