<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $order_id
 * @property int $drink_id
 * @property int $count
 * @property-read Drink $drink
 * @property-read Order $order
 * @method static Builder<static>|OrderDrink newModelQuery()
 * @method static Builder<static>|OrderDrink newQuery()
 * @method static Builder<static>|OrderDrink query()
 * @method static Builder<static>|OrderDrink whereCount($value)
 * @method static Builder<static>|OrderDrink whereCreatedAt($value)
 * @method static Builder<static>|OrderDrink whereDrinkId($value)
 * @method static Builder<static>|OrderDrink whereId($value)
 * @method static Builder<static>|OrderDrink whereOrderId($value)
 * @method static Builder<static>|OrderDrink whereUpdatedAt($value)
 * @method static \Database\Factories\OrderDrinkFactory factory($count = null, $state = [])
 * @mixin Eloquent
 */
class OrderDrink extends Model
{
    use HasFactory;

    protected $table = 'orders_drinks';

    protected $fillable = ['order_id', 'drink_id', 'count'];

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function drink() : BelongsTo
    {
        return $this->belongsTo(Drink::class);
    }
}
