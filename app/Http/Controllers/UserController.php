<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderDrink;
use App\Models\OrderHistory;
use App\Models\OrderPizza;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponses;

    public function orders() : JsonResponse
    {

        $orderDrinks = OrderDrink::whereHas('order', function ($query) { $query->where('user_id', auth()->id()); })
            ->with(['order', 'drink'])
            ->get()
            ->map(function ($orderDrink) {
                return [
                    'name'          => $orderDrink->drink->name,
                    'price'         => $orderDrink->drink->price,
                    'count'         => $orderDrink->count,
                    'status'        => $orderDrink->order->status,
                    'created_at'    => $orderDrink->order->created_at,
                    'totalPrice'    => $orderDrink->drink->price * $orderDrink->count,
                ];
            });
        $orderPizzas = OrderPizza::whereHas('order', function ($query) { $query->where('user_id', auth()->id()); })
            ->with(['order', 'pizza'])
            ->get()
            ->map(function ($orderPizza) {
                return [
                    'name'          => $orderPizza->pizza->name,
                    'price'         => $orderPizza->pizza->price,
                    'count'         => $orderPizza->count,
                    'status'        => $orderPizza->order->status,
                    'created_at'    => $orderPizza->order->created_at,
                    'totalPrice'    => $orderPizza->pizza->price * $orderPizza->count,
                ];
            });

        return $this->success([
            'pizzas' => $orderPizzas,
            'orderDrinks' => $orderDrinks
        ], 'Success retrieve orders!');
    }

    public function history(): JsonResponse
    {
        $history = OrderHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($history, 'Order history retrieved');
    }
}
