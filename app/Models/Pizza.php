<?php

namespace App\Models;

use Database\Factories\PizzaFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $name
 * @property float $price
 * @property-read Collection<int, Order> $orders
 * @property-read int|null $orders_count
 * @method static PizzaFactory factory($count = null, $state = [])
 * @method static Builder<static>|Pizza newModelQuery()
 * @method static Builder<static>|Pizza newQuery()
 * @method static Builder<static>|Pizza query()
 * @method static Builder<static>|Pizza whereCreatedAt($value)
 * @method static Builder<static>|Pizza whereId($value)
 * @method static Builder<static>|Pizza whereName($value)
 * @method static Builder<static>|Pizza wherePrice($value)
 * @method static Builder<static>|Pizza whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Pizza extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public function orders() : BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'orders_pizzas')
            ->withPivot('count')
            ->withTimestamps();
    }
}
