<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $status
 * @property string $address
 * @property \Illuminate\Support\Carbon $delivery_time
 * @property array<array-key, mixed> $items
 * @property numeric $total_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OrderHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereItems($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory whereUserId($value)
 * @mixin \Eloquent
 */
class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'user_id', 'status', 'address', 'delivery_time', 'items', 'total_price'];

    protected $casts = [
        'items' => 'array',
        'delivery_time' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
