<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status,
            'address'       => $this->address,
            'created_at'    => $this->created_at?->toDateTimeString(),
            'delivery_time' => $this->delivery_time?->toDateTimeString(),
            'total_price'   => $this->products->sum(fn($p) => $p->price * $p->pivot->count),
            'items_count'   => $this->products->count(),
        ];
    }
}
