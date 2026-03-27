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
        $orders = Order::where('user_id', auth()->id())
            ->with(['pizzas', 'drinks'])
            ->get();

        return $this->success($orders, 'Success retrieve orders!');
    }

    public function history(): JsonResponse
    {
        $history = OrderHistory::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->success($history, 'Order history retrieved');
    }
}
