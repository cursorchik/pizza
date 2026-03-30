<?php

namespace App\Services;

use App\Models\OrderHistory;
use Illuminate\Database\Eloquent\Collection;

class OrderHistoryService
{
    public function getUserHistory(int $userId) : Collection { return OrderHistory::where('user_id', $userId)->orderBy('created_at', 'desc')->get(); }

    public function getOrderHistory(int $orderId) : ?OrderHistory { return OrderHistory::where('order_id', $orderId)->first(); }
}
