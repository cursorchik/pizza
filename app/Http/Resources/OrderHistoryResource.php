<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'order_id'      => $this->order_id,
            'status'        => $this->status,
            'address'       => $this->address,
            'delivery_time' => $this->delivery_time?->toDateTimeString(),
            'items'         => $this->items,
            'total_price'   => (float) $this->total_price,
            'created_at'    => $this->created_at?->toDateTimeString(),
        ];
    }
}
