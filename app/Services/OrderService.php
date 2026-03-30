<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderHistory;

use App\Models\Product;
use App\Services\CartService;

use Exception;
use Illuminate\Support\Collection;

class OrderService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @throws Exception
     */
    protected function checkLimits(array $cart, Collection $products) : void
    {
        $totalPizzas = $products->filter(fn($p) => $p->category->slug === 'pizza')->sum('quantity');
        $totalDrinks = $products->filter(fn($p) => $p->category->slug === 'drinks')->sum('quantity');

        if ($totalPizzas > 10) throw new Exception('Maximum 10 pizzas allowed in cart', 422);
        if ($totalDrinks > 20) throw new Exception('Maximum 20 drinks allowed in cart', 422);
    }

    /**
     * @throws Exception
     */
    public function createFromCart(int $userId, string $address) : Order
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) throw new Exception('Cart is empty', 400);

        $products = $this->cartService->getCartProducts();

        if ($products->isEmpty()) throw new Exception('Products not found', 400);

        $this->checkLimits($cart, $products);

        $deliveryTimes = [30, 40, 50, 60, 70, 80, 90, 100, 110, 120];
        $randomTime = $deliveryTimes[array_rand($deliveryTimes)];

        $order = Order::create([
            'user_id'       => $userId,
            'address'       => $address,
            'status'        => 'preparing',
            'delivery_time' => now()->addMinutes($randomTime),
        ]);

        $attachData = [];
        foreach ($products as $product) $attachData[$product->id] = ['count' => $product->quantity];
        $order->products()->attach($attachData);

        $this->cartService->clearCart();

        return $order;
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $order->update(['status' => $newStatus]);

        if (in_array($newStatus, ['delivered', 'cancelled'])) $this->saveToHistory($order, $newStatus);

        return $order->fresh();
    }

    public function getUserOrders(int $userId): Collection
    {
        return Order::where('user_id', $userId)
            ->with(['products' => fn($q) => $q->withPivot('count')->with('category', 'attributes')])
            ->get();
    }

    protected function saveToHistory(Order $order, string $status) : void
    {
        $order->load(['products' => fn($q) => $q->withPivot('count')->with('category', 'attributes')]);

        $items = $order->products->map(fn($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'count' => $p->pivot->count,
            'total' => $p->price * $p->pivot->count,
            'type' => $p->category->slug,
            'attributes' => $p->attributes,
        ]);

        $totalPrice = $order->products->sum(fn($p) => $p->price * $p->pivot->count);

        OrderHistory::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'status' => $status,
            'address' => $order->address,
            'delivery_time' => $order->delivery_time,
            'items' => $items,
            'total_price' => $totalPrice,
        ]);
    }
}
