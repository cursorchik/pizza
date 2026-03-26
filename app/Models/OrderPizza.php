<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $order_id
 * @property int $pizza_id
 * @property int $count
 * @property-read Order $order
 * @property-read Pizza $pizza
 * @method static Builder<static>|OrderPizza newModelQuery()
 * @method static Builder<static>|OrderPizza newQuery()
 * @method static Builder<static>|OrderPizza query()
 * @method static Builder<static>|OrderPizza whereCount($value)
 * @method static Builder<static>|OrderPizza whereCreatedAt($value)
 * @method static Builder<static>|OrderPizza whereId($value)
 * @method static Builder<static>|OrderPizza whereOrderId($value)
 * @method static Builder<static>|OrderPizza wherePizzaId($value)
 * @method static Builder<static>|OrderPizza whereUpdatedAt($value)
 * @method static \Database\Factories\OrderPizzaFactory factory($count = null, $state = [])
 * @mixin \Eloquent
 */
class OrderPizza extends Model
{
    use HasFactory;

    protected $table = 'orders_pizzas';

    protected $fillable = ['order_id', 'pizza_id', 'count'];

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function pizza() : BelongsTo
    {
        return $this->belongsTo(Pizza::class);
    }
}
