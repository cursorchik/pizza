<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponses;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService) { $this->orderService = $orderService; }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        try
        {
            $order = $this->orderService->createFromCart(auth()->id(), $request->address);
            $order->load(['products' => fn($q) => $q->withPivot('count')->with('category', 'attributes')]);
            return new OrderResource($order)->response()->setStatusCode(201);
        }
        catch (\Exception $e)
        {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $order->load(['products' => fn($q) => $q->withPivot('count')->with('category', 'attributes')]);
        return new OrderResource($order)->response();
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);
        $updated = $this->orderService->updateStatus($order, $request->status);
        $updated->load(['products' => fn($q) => $q->withPivot('count')->with('category', 'attributes')]);
        return new OrderResource($updated)->response();
    }
}
