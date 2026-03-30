<?php
namespace App\Http\Controllers;

use App\Http\Resources\OrderHistoryResource;
use App\Http\Resources\UserOrderResource;
use App\Services\OrderHistoryService;
use App\Services\OrderService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponses;

    protected OrderService $orderService;
    protected OrderHistoryService $historyService;

    public function __construct(OrderService $orderService, OrderHistoryService $historyService)
    {
        $this->orderService = $orderService;
        $this->historyService = $historyService;
    }

    public function orders(): JsonResponse
    {
        $orders = $this->orderService->getUserOrders(auth()->id());
        return UserOrderResource::collection($orders)->response();
    }

    public function history(): JsonResponse
    {
        $history = $this->historyService->getUserHistory(auth()->id());
        return OrderHistoryResource::collection($history)->response();
    }
}
