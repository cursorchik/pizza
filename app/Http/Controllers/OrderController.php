<?php

namespace App\Http\Controllers;

use App\Models\OrderHistory;
use Illuminate\Validation\Rule;
use Session;
use Throwable;

use App\Models\Order;
use App\Models\OrderDrink;
use App\Models\OrderPizza;

use App\Interfaces\IOrder;
use App\Traits\ApiResponses;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller implements IOrder
{
    use ApiResponses;

    public function store(Request $request) : JsonResponse
    {
        if ($cart = Session::get('cart', []))
        {
            $request->validate(['address' => 'required|string|max:255']);

            $deliveryTimes = [30, 40, 50, 60, 70, 80, 90, 100, 110, 120];
            $randomTime = $deliveryTimes[array_rand($deliveryTimes)];

//            DB::beginTransaction();

            $order = Order::create([
                'user_id'       => auth()->user()->id,
                'address'       => $request->address,
                'status'        => 'preparing',
                'delivery_time' => now()->addMinutes($randomTime),
            ]);

            $items = CartController::formatCart($cart);
            foreach ($items as $item)
            {
                if ($item['type'] == 'pizza')
                {
                    OrderPizza::create(['order_id' => $order->id, 'pizza_id' => $item['id'], 'count' => $item['quantity']]);
                }
                else
                {
                    OrderDrink::create(['order_id' => $order->id, 'drink_id' => $item['id'], 'count' => $item['quantity']]);
                }
            }

            return $this->success($order, "Order #{$order->id} successfully created!");
        }

        return $this->error('Cart is empty!');
    }

    public function show(Order $order) : JsonResponse
    {
        if ($order->user_id !== auth()->id() && !auth()->user()->is_admin) return $this->error('Unauthorized', 403);

        $order->load([
            'pizzas' => fn($q) => $q->withPivot('count'),
            'drinks' => fn($q) => $q->withPivot('count'),
        ]);

        $data = [
            'id'            => $order->id,
            'status'        => $order->status,
            'address'       => $order->address,
            'created_at'    => $order->created_at->toDateTimeString(),
            'delivery_time' => $order->delivery_time->toDateTimeString(),
            'items'         => [
                'pizzas' => $order->pizzas->map(fn($pizza) => [
                    'name'  => $pizza->name,
                    'price' => $pizza->price,
                    'count' => $pizza->pivot->count,
                    'total' => $pizza->price * $pizza->pivot->count,
                ]),
                'drinks' => $order->drinks->map(fn($drink) => [
                    'name'  => $drink->name,
                    'price' => $drink->price,
                    'count' => $drink->pivot->count,
                    'total' => $drink->price * $drink->pivot->count,
                ]),
            ],
            'total_price' => $order->pizzas->sum(fn($p) => $p->price * $p->pivot->count) + $order->drinks->sum(fn($d) => $d->price * $d->pivot->count),
        ];

        return $this->success($data, "Success retrieve order #{$order->id}!");
    }

    public function update(Request $request, Order $order) : JsonResponse
    {
        if (!auth()->user()->is_admin) return $this->error('Unauthorized', 403);

        $request->validate(['status' => ['required', 'string', Rule::in(self::STATUSES)]]);

        $newStatus = $request->status;
        $order->update(['status' => $newStatus]);

        if (in_array($newStatus, [self::STATUS_DELIVERED, self::STATUS_CANCELLED]))
        {
            $order->load([
                'pizzas' => fn($q) => $q->withPivot('count'),
                'drinks' => fn($q) => $q->withPivot('count'),
            ]);

            $items = [
                'pizzas' => $order->pizzas->map(fn($pizza) => [
                    'name'  => $pizza->name,
                    'price' => $pizza->price,
                    'count' => $pizza->pivot->count,
                    'total' => $pizza->price * $pizza->pivot->count,
                ])->values(),
                'drinks' => $order->drinks->map(fn($drink) => [
                    'name'  => $drink->name,
                    'price' => $drink->price,
                    'count' => $drink->pivot->count,
                    'total' => $drink->price * $drink->pivot->count,
                ])->values(),
            ];

            $totalPrice = $order->pizzas->sum(fn($p) => $p->price * $p->pivot->count) + $order->drinks->sum(fn($d) => $d->price * $d->pivot->count);

            OrderHistory::create([
                'order_id'      => $order->id,
                'user_id'       => $order->user_id,
                'status'        => $newStatus,
                'address'       => $order->address,
                'delivery_time' => $order->delivery_time,
                'items'         => $items,
                'total_price'   => $totalPrice,
            ]);
        }

        return $this->success($order, "Success update order #{$order->id}!");
    }
}
