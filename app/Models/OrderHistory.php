<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Order|null $order
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderHistory query()
 * @mixin \Eloquent
 */
class OrderHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'user_id', 'status', 'address', 'delivery_time', 'items', 'total_price'
    ];

    protected $casts = [
        'items' => 'array',
        'delivery_time' => 'datetime',
    ];

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
